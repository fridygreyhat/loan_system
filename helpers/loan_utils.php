<?php
/**
 * Shared loan calculations.
 */

/**
 * Maturity date = release date + loan duration.
 *
 * @param string|null $releaseDate  Y-m-d
 * @param int|null    $duration     number of periods
 * @param string|null $durationUnit "Days", "Weeks", "Months" or "Years" (case-insensitive, singular or plural)
 * @return string|null Y-m-d, or null when the release date is missing/invalid
 */
function compute_maturity_date(?string $releaseDate, ?int $duration, ?string $durationUnit): ?string
{
    if (!$releaseDate || !($date = date_create($releaseDate))) {
        return null;
    }
    return add_period(DateTimeImmutable::createFromInterface($date), normalize_period($durationUnit), max(0, (int) $duration))->format('Y-m-d');
}

/**
 * Add $count periods to a date. Month-based periods clamp to the end of the
 * month (Jan 31 + 1 month = Feb 28), unlike DateInterval which overflows.
 */
function add_period(DateTimeImmutable $start, string $period, int $count): DateTimeImmutable
{
    $days = ['day' => 1, 'week' => 7, 'biweekly' => 14][$period] ?? null;
    if ($days !== null) {
        return $start->modify('+' . ($days * $count) . ' days');
    }
    $months = $count * (['quarterly' => 3, 'year' => 12][$period] ?? 1);
    $first = $start->modify('first day of this month')->modify("+{$months} months");
    $day = min((int) $start->format('j'), (int) $first->format('t'));
    return $first->setDate((int) $first->format('Y'), (int) $first->format('n'), $day);
}

/** Loan status values used across the application. */
const LOAN_STATUSES = ['pending', 'approved', 'denied', 'paid', 'defaulted'];

/** "Bi-Weekly" -> "biweekly", "Months" -> "month", etc. */
function normalize_period(?string $value): string
{
    $v = strtolower(str_replace(['-', ' ', '_'], '', (string) $value));
    return match (true) {
        str_starts_with($v, 'daily') || str_starts_with($v, 'day') => 'day',
        str_starts_with($v, 'biweek') => 'biweekly',
        str_starts_with($v, 'week') => 'week',
        str_starts_with($v, 'quarter') => 'quarterly',
        str_starts_with($v, 'year') => 'year',
        default => 'month',
    };
}

/**
 * Build an instalment schedule. Mirrors the maths in loans/loan_calculator.php
 * so the stored schedule matches what staff see in the calculator.
 *
 * @return array<int, array{installment_no:int, due_date:string, principal_due:float, interest_due:float, amount_due:float}>
 */
function build_loan_schedule(array $loan): array
{
    $principal = (float) ($loan['principal_amount'] ?? 0);
    $release = $loan['release_date'] ?? null;
    if ($principal <= 0 || !$release || !date_create($release)) {
        return [];
    }

    $rate = (float) ($loan['loan_interest'] ?? 0);
    $ratePeriod = normalize_period($loan['loan_interest_pd'] ?? 'Month');
    $duration = max(1, (int) ($loan['loan_duration'] ?? 1));
    $durationPeriod = normalize_period($loan['loan_duration_pd'] ?? 'Months');
    $cycle = normalize_period($loan['repayment_cycle'] ?? 'Monthly');
    $method = $loan['interest_mtd'] ?? 'flat_rate';

    $monthsIn = ['day' => 1 / 30, 'week' => 7 / 30, 'biweekly' => 14 / 30, 'month' => 1, 'quarterly' => 3, 'year' => 12];
    $daysIn = ['day' => 1, 'week' => 7, 'biweekly' => 14, 'month' => 30, 'quarterly' => 91, 'year' => 365];

    $n = (int) ($loan['no_repayment_cycle'] ?? 0);
    if ($n <= 0) {
        $n = max(1, (int) round(($duration * $monthsIn[$durationPeriod]) / $monthsIn[$cycle]));
    }

    $r = $rate / 100 * $daysIn[$cycle] / $daysIn[$ratePeriod];   // rate per repayment cycle

    $start = new DateTimeImmutable($release);

    $rows = [];
    $balance = $principal;
    $level = null;
    if ($method === 'reducing_balance') {
        $level = $r == 0 ? $principal / $n : $principal * $r * (1 + $r) ** $n / ((1 + $r) ** $n - 1);
    } elseif ($method === 'compound_interest') {
        $level = $principal * (1 + $r) ** $n / $n;
    }

    for ($i = 1; $i <= $n; $i++) {
        $date = add_period($start, $cycle, $i);
        switch ($method) {
            case 'reducing_balance':
                $interest = $balance * $r;
                $princ = $level - $interest;
                break;
            case 'compound_interest':
                $princ = $principal / $n;
                $interest = $level - $princ;
                break;
            case 'interest_only':
                $interest = $principal * $r;
                $princ = $i === $n ? $principal : 0.0;
                break;
            default: // flat_rate
                $interest = $principal * $r;
                $princ = $principal / $n;
        }
        $princ = $i === $n ? $balance : round($princ, 2);   // last row absorbs rounding
        $interest = round($interest, 2);
        $balance = round($balance - $princ, 2);
        $rows[] = [
            'installment_no' => $i,
            'due_date' => $date->format('Y-m-d'),
            'principal_due' => $princ,
            'interest_due' => $interest,
            'amount_due' => round($princ + $interest, 2),
        ];
    }
    return $rows;
}

/**
 * (Re)generate loan_schedule rows for a loan. Existing rows are replaced.
 * Call inside the caller's transaction where there is one.
 */
function regenerate_loan_schedule(PDO $con, int $loanId): int
{
    $stmt = $con->prepare("SELECT * FROM loans WHERE id = ?");
    $stmt->execute([$loanId]);
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);

    $con->prepare("DELETE FROM loan_schedule WHERE loan_id = ?")->execute([$loanId]);
    if (!$loan || !in_array($loan['status'], ['approved', 'paid', 'defaulted'], true)) {
        return 0;
    }

    $insert = $con->prepare("INSERT INTO loan_schedule (loan_id, installment_no, due_date, principal_due, interest_due, amount_due) VALUES (?, ?, ?, ?, ?, ?)");
    $rows = build_loan_schedule($loan);
    foreach ($rows as $row) {
        $insert->execute([$loanId, $row['installment_no'], $row['due_date'], $row['principal_due'], $row['interest_due'], $row['amount_due']]);
    }
    return count($rows);
}
