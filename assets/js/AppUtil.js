var AppUtil = {
    /**
     * Makes ajax calls 
     * @param {string} imglo Id of the gif IMage that shows Loading..e.g #loader
     * @param {string} mtd p=POST and g=Get
     * @param {string} url where the request is sent
     * @param {string} datatype e.g json ot text
     * @param {Object} data data sent with request e.g {name:"ken",school:"muk"}
     * @param {string} success  method executed on success
     * @returns {undefined} void
     */
    ajax: function (imglo, mtd, url, datatype, data, success) {
        var showLoader = false;
        if (imglo != "") {
            var loader = $(imglo);
            showLoader = true;
        }
        var mtdf = "POST";
        if (mtd == "p") {
            mtdf = "POST";
        } else {
            mtdf = "GET";
        }
        if (showLoader) {
            loader.show();
        }
        jQuery.ajax({
            type: mtdf, // HTTP method POST or GET
            url: url, //Where to make Ajax calls
            dataType: datatype, // Data tr_, HTML, json etc.
            data: data, //Form variables,
            success: function (response) {
                //console.log(response);
                if (showLoader) {
                    loader.fadeOut(1000);
                }
                success(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error ");
                //console.log(xhr.responseText);
                console.log(xhr.responseText);
                console.log(ajaxOptions);
                console.log(thrownError);
                if (showLoader) {
                    loader.hide(100);
                }

            }

        });
    },
    /**
     * Makes ajax calls 
     * @param {string} imglo Id of the gif IMage that shows Loading..e.g #loader
     * @param {string} mtd p=POST and g=Get
     * @param {string} url where the request is sent
     * @param {string} datatype e.g json ot text
     * @param {Object} data data sent with request e.g {name:"ken",school:"muk"}
     * @param {string} success  method executed on success
     * @param {string} progress method to show progress.
     * @returns {undefined} void
     */
    ajaxWithProgress: function (imglo, mtd, url, datatype, data, success, progress) {
        var showLoader = false;
        if (imglo != "") {
            var loader = $(imglo);
            showLoader = true;
        }
        var mtdf = "POST";
        if (mtd == "p") {
            mtdf = "POST";
        } else {
            mtdf = "GET";
        }
        if (showLoader) {
            loader.show();
        }
        jQuery.ajax({
            type: mtdf, // HTTP method POST or GET
            url: url, //Where to make Ajax calls
            dataType: datatype, // Data tr_, HTML, json etc.
            data: data, //Form variables,
            success: function (response) {
                //console.log(response);
                if (showLoader) {
                    loader.fadeOut(1000);
                }
                success(response);
            },
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                
               xhr.upload.addEventListener("progress", function(evt){
                   console.log("Upload ***");
                   console.log(evt.lengthComputable); // false
                    progress(evt);
               },false);
                //Download progress
                xhr.addEventListener("progress", function (evt) {
                     console.log("Download ***");
                    console.log(evt.lengthComputable); // false
                    progress(evt);
                  
                }, false);
                return xhr;
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error ");
                //console.log(xhr.responseText);
                console.log(xhr.responseText);
                console.log(ajaxOptions);
                console.log(thrownError);
                if (showLoader) {
                    loader.hide(100);
                }

            }

        });
    },
    /**
     * Makes ajax With Params in Sucess  calls 
     * @param {string} imglo Id of the gif IMage that shows Loading..e.g #loader
     * @param {string} mtd p=POST and g=Get
     * @param {string} url where the request is sent
     * @param {string} datatype e.g json ot text
     * @param {Object} data data sent with request e.g {name:"ken",school:"muk"}
     * @param {string} success  method executed on success
     * @returns {undefined} void
     */
    ajaxWithParams: function (imglo, mtd, url, datatype, data, callBack, params) {
        var showLoader = false;
        if (imglo != "") {
            var loader = $(imglo);
            showLoader = true;
        }
        var mtdf = "POST";
        if (mtd == "p") {
            mtdf = "POST";
        } else {
            mtdf = "GET";
        }
        if (showLoader) {
            loader.show();
        }
        jQuery.ajax({
            type: mtdf, // HTTP method POST or GET
            url: url, //Where to make Ajax calls
            dataType: datatype, // Data tr_, HTML, json etc.
            data: data, //Form variables,
            success: function (response) {
                //console.log(response);
                if (showLoader) {
                    loader.fadeOut(1000);
                }
                callBack.apply(this, [response, params]);//callback.apply(this, args);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error ");
                //console.log(xhr.responseText);
                console.log(xhr.responseText);
                console.log(ajaxOptions);
                console.log(thrownError);
                if (showLoader) {
                    loader.hide(100);
                }

            }

        });
    },
    ajaxWithFiles: function (imglo, mtd, url, datatype, data, success) {
        var loader = $(imglo);
        var mtdf = "POST";
        if (mtd == "p") {
            mtdf = "POST";
        } else {
            mtdf = "GET";
        }
        loader.show();
        jQuery.ajax({
            type: mtdf, // HTTP method POST or GET
            url: url, //Where to make Ajax calls
            dataType: datatype, // Data tr_, HTML, json etc.
            data: data, //Form variables,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                //console.log(response);
                loader.fadeOut(1000);
                success(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log("Error ");
                //console.log(xhr.responseText);
                console.log(xhr.responseText);
                console.log(ajaxOptions);
                console.log(thrownError);
                loader.hide(100);

            }

        });
    },
    numbertabletd: function (sel) {
        $(sel).each(function (i) {
            $(this).find(">:first-child").text((++i));
        });
    },
    validateEmail: function validateEmail(emailID)
    {
        var atpos = emailID.indexOf("@");
        var dotpos = emailID.lastIndexOf(".");
        var afterdot = (emailID.substr((new Number(dotpos))) != undefined) ? emailID.substr((new Number(dotpos) + 1)).length : 0;
        if (atpos < 1 || (dotpos - atpos < 2) || afterdot == 0)
        {
            return false;
        }
        return(true);
    },
    loadDatePicker: function (sel) {
        $(sel).daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }


};

$.fn.exists = function () {
    return this.length !== 0;
}

