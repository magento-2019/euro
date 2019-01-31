/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Ophirah
 * @package     Qquoteadv
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (http://www.cart2quote.com)
 * @license     http://www.cart2quote.com/ordering-licenses
 */

function prepareIE(height, overflow) {
    bod = document.getElementsByTagName('body')[0];
    bod.style.height = height;
    bod.style.overflow = overflow;

    htm = document.getElementsByTagName('html')[0];
    htm.style.height = height;
    htm.style.overflow = overflow;
}

function initMsg() {
    bod = document.getElementsByTagName('body')[0];
    overlay = document.createElement('div');
    overlay.id = 'overlay';

    bod.appendChild(overlay);
    $('overlay').style.display = 'block';
    try {
        $('lightbox1').style.display = 'block';
    } catch (e) {
    }
    prepareIE("auto", "auto");
}
function cancelMsg() {
    bod = document.getElementsByTagName('body')[0];
    olddiv = document.getElementById('overlay');
    if (overlay) {
        bod.removeChild(olddiv);
    }
}

/**
 * Function creates an iframe as an alternative method to add a product to the quote using ajax when it contains a file
 * This is only needed when there is no jQuery >= 1.6 available
 *
 * @param src
 */
function prepareFrameAndForm(src, form) {
    //create iframe
    iframe = document.createElement("iframe");
    iframe.setAttribute("id", "upload");
    iframe.setAttribute("name", "upload");
    iframe.style.width = 0+"px";
    iframe.style.height = 0+"px";
    iframe.onload = uploadFrameReturn;
    document.body.appendChild(iframe);

    //create input
    input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("value", "true");
    input.setAttribute("name", "base64");

    //change form
    form.action = src;
    form.target = 'upload';
    form.appendChild(input);
}

/**
 * Function that handles the simulated succes call of the iframe ajax emulation
 * This should work on most proweser including IE<=9
 */
function uploadFrameReturn() {
    var transport = frames['upload'].document.getElementsByTagName("body")[0].innerText;
    if(transport.length > 0) {
        if (typeof atob === "undefined") {
            //pleas upgrade your browser... or allow/upgrade jQuery...
            // Create Base64 Object Thanks to: https://gist.github.com/ncerminara/11257943
            var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
            data = Base64.decode(transport);
        } else {
            data = atob(transport);
        }

        data = data.evalJSON();
        if (data['result'] == 1) {
            if ($(document.body).hasClassName('top-link-qquoteadv')) {
                $$('a.top-link-qquoteadv')[0].update(data['itemstext']);
            }
            document.getElementById('lightbox2').innerHTML = data['html'];
        } else {
            document.location.href = data['producturl']
        }
    } else {
        console.log("c2q legacy ajax did not return a result");
    }
}

/**
 * Function that adds a product to the quote
 * It uses ajax is the ajax var is set
 *
 * @param url
 * @param ajax
 */
function addQuote(url, ajax) {
    frmAdd2Cart = $('product_addtocart_form');
    if (frmAdd2Cart) {
        var validator = new Validation(frmAdd2Cart);
        if (validator) {
            if (validator.validate()) {
                if (ajax == 1) {
                    initMsg();
                    lightbox2 = document.createElement('div');
                    lightbox2.id = "lightbox2";
                    overlay.appendChild(lightbox2);

                    lightboxload = document.createElement('div');
                    lightboxload.id = "lightboxload";
                    lightbox2.appendChild(lightboxload);
                    frmValues = frmAdd2Cart.serialize(true);

                    //check if we want to upload a file
                    if (frmAdd2Cart.select('[type="file"]').size() > 0) {
                        //ajax file upload
                        var versnums;
                        if ((typeof jQuery != 'undefined') && (vernums = jQuery.fn.jquery.split('.')) && (parseInt(vernums[0]) >= 1 && parseInt(vernums[1]) >= 6 && parseInt(vernums[2]) > 0)) {
                            //jQuery is available and above 1.6.0
                            var form = jQuery('#product_addtocart_form')[0];
                            var formData = new FormData(form);
                            jQuery.ajax({
                                url: url,
                                type: "POST",
                                data: formData,
                                //works from jQuery 1.6
                                // THIS MUST BE DONE FOR FILE UPLOADING
                                contentType: false,
                                processData: false,
                                // ... Other options like success and etc
                                success: function (msg) {
                                    data = jQuery.parseJSON(msg);
                                    if (data['result'] == 1) {
                                        if ($(document.body).hasClassName('top-link-qquoteadv')) {
                                            $$('a.top-link-qquoteadv')[0].update(data['itemstext']);
                                        }
                                        document.getElementById('lightbox2').innerHTML = data['html'];
                                    } else {
                                        document.location.href = data['producturl']
                                    }
                                }
                            });
                        } else {
                            //works if jQuery is not available even in IE<=9
                            prepareFrameAndForm(url, frmAdd2Cart);
                            console.log(frmAdd2Cart);
                            frmAdd2Cart.submit();
                        }
                    } else {
                        //no file upload, use normal function
                        new Ajax.Request(url, {
                            parameters: frmValues,
                            method: 'post',
                            evalJSON: 'force',
                            onSuccess: function (transport) {
                                data = transport.responseJSON;
                                if (data['result'] == 1) {
                                    if ($(document.body).hasClassName('top-link-qquoteadv')) {
                                        $$('a.top-link-qquoteadv')[0].update(data['itemstext']);
                                    }
                                    document.getElementById('lightbox2').innerHTML = data['html'];
                                } else {
                                    document.location.href = data['producturl']
                                }
                            }
                        });
                    }
                } else {
                    frmAdd2Cart.writeAttribute('action', url);
                    frmAdd2Cart.submit();
                }
            }
        }
    }
}

function addQuoteList(url, ajax) {

    if (url.indexOf("c2qredirect") != -1) {
        document.location.href = url;
    } else {

        if (ajax == 1) {
            initMsg();
            lightbox2 = document.createElement('div');
            lightbox2.id = "lightbox2";
            overlay.appendChild(lightbox2);

            lightboxload = document.createElement('div');
            lightboxload.id = "lightboxload";
            lightbox2.appendChild(lightboxload);


            new Ajax.Request(url, {
                method: 'post',
                evalJSON: 'force',
                onSuccess: function (transport) {
                    data = transport.responseJSON;
                    if (data['result'] == 1) {
                        if($(document.body).hasClassName('top-link-qquoteadv')){
                            $$('a.top-link-qquoteadv')[0].update(data['itemstext']);
                        }
                        document.getElementById('lightbox2').innerHTML = data['html'];
                    } else {
                        document.location.href = data['producturl']
                    }
                }
            });
        } else {
            frmAdd2Cart.writeAttribute('action', url);
            frmAdd2Cart.submit();
        }
    }

}

function isExistUserEmail(event, url, errorMsg) {
    elmEmail = Event.element(event);
    elmEmailMsg = $('email_message');
    loaderEmailDiv = $("please-wait");
    btnSubm = $('submitOrder');

    if (btnSubm) {
        btnSubm.disabled = false;
    }

    val = $F(elmEmail);  //$F('customer:email');
    var pars = 'email=' + val;

    //loader
    loaderEmailDiv.show();

    new Ajax.Request(url, {
        method: 'post',
        parameters: pars,
        //onCreate: function() {  },
        onSuccess: function (transport) {
            var responseStr = transport.responseText;
            if (responseStr == 'exists') {
                elmEmailMsg.show();
                elmEmailMsg.innerHTML = errorMsg;
                elmEmailMsg.addClassName("validation-advice");

                if ($('advice-required-entry-customer:email')) $('advice-required-entry-customer:email').hide();
                if ($('advice-validate-email-customer:email')) $('advice-validate-email-customer:email').hide();

                elmEmail.addClassName('validation-failed');
                if (btnSubm) {
                    btnSubm.setStyle({background: '#dddddd'});
                    btnSubm.disabled = true;
                }

            } else {
                elmEmailMsg.hide();
                elmEmailMsg.removeClassName("validation-advice");
            }
            loaderEmailDiv.hide();
        },
        onFailure: function () {
            loaderEmailDiv.hide();
            alert('Connection Error. Try again later.');
        }
    });

    return(false);
}

function adminLogin(url) {
    var windowHandle = popupwindow(url, '_blank', 850, 600);

    var timer = setInterval(function () {
        if (!windowHandle || windowHandle.closed) {
            clearInterval(timer);
            return;
        }

        if (windowHandle.location.href.substr(0, 4) != 'http') {
            return;
        }

        if (windowHandle.location.href == url) {
            return;
        }

        windowHandle.close();
        window.location.reload();
        clearInterval(timer);
    }, 50);
}

function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

/**
 * This function fixes an issue with Magento CE 1.9.1.0 (And probably Magento EE 1.14.1.0)
 * On configurable products the onclick on tje Add to Quote button got replaced by the
 * onclick from the Add to Cart button.
 */
function overWriteConfigurableSwatches(){
    // rewrite the setStockData method from /js/configurableswatches/swatches-product.js; it would also select the quote button
    if(typeof Product !== "undefined") {
        if(typeof(Product.ConfigurableSwatches) !== "undefined") {
            if (typeof(Product.ConfigurableSwatches.prototype) === "object") {
                //only overwrite if it exists
                Product.ConfigurableSwatches.prototype.setStockData = function () {
                    //var cartBtn = $$('.add-to-cart button.button');
                    var cartBtn = $$('.add-to-cart button.button:not(.btn-quote)');
                    this._E.cartBtn = {
                        btn: cartBtn,
                        txt: cartBtn.invoke('readAttribute', 'title'),
                        onclick: cartBtn.length ? cartBtn[0].getAttribute('onclick') : ''
                    };
                    //add quote button
                    var qtBtn = $$('.add-to-cart button.button.btn-quote');
                    this._E.qtBtn = {
                        btn: qtBtn,
                        txt: qtBtn.invoke('readAttribute', 'title'),
                        onclick: qtBtn.length ? qtBtn[0].getAttribute('onclick') : ''
                    };
                    this._E.availability = $$('p.availability');
                    // Set cart button event
                    this._E.cartBtn.btn.invoke('up').invoke('observe', 'mouseenter', function () {
                        clearTimeout(this._N.resetTimeout);
                        this.resetAvailableOptions();
                    }.bind(this));
                    // Set Quote button event
                    this._E.qtBtn.btn.invoke('up').invoke('observe', 'mouseenter', function () {
                        clearTimeout(this._N.resetTimeout);
                        this.resetAvailableOptions();
                    }.bind(this));
                };

                Product.ConfigurableSwatches.prototype.setStockStatus = function (inStock) {
                    if (inStock) {
                        this._E.availability.each(function (el) {
                            var el = $(el);
                            el.addClassName('in-stock').removeClassName('out-of-stock');
                            el.select('span').invoke('update', Translator.translate('In Stock'));
                        });

                        this._E.cartBtn.btn.each(function (el, index) {
                            var el = $(el);
                            el.disabled = false;
                            el.removeClassName('out-of-stock');
                            el.writeAttribute('onclick', this._E.cartBtn.onclick);
                            el.title = '' + Translator.translate(this._E.cartBtn.txt[index]);
                            el.select('span span').invoke('update', Translator.translate(this._E.cartBtn.txt[index]));
                        }.bind(this));
                        this._E.qtBtn.btn.each(function (el, index) {
                            var el = $(el);
                            el.disabled = false;
                            el.removeClassName('out-of-stock');
                            el.writeAttribute('onclick', this._E.qtBtn.onclick);
                            el.title = '' + Translator.translate(this._E.qtBtn.txt[index]);
                            el.select('span span').invoke('update', Translator.translate(this._E.qtBtn.txt[index]));
                        }.bind(this));
                    } else {
                        this._E.availability.each(function (el) {
                            var el = $(el);
                            el.addClassName('out-of-stock').removeClassName('in-stock');
                            el.select('span').invoke('update', Translator.translate('Out of Stock'));
                        });
                        this._E.cartBtn.btn.each(function (el) {
                            var el = $(el);
                            el.addClassName('out-of-stock');
                            el.disabled = true;
                            el.removeAttribute('onclick');
                            el.observe('click', function (event) {
                                Event.stop(event);
                                return false;
                            });
                            el.writeAttribute('title', Translator.translate('Out of Stock'));
                            el.select('span span').invoke('update', Translator.translate('Out of Stock'));
                        });
                        this._E.qtBtn.btn.each(function (el) {
                            var el = $(el);
                            el.addClassName('out-of-stock');
                            el.disabled = true;
                            el.removeAttribute('onclick');
                            el.observe('click', function (event) {
                                Event.stop(event);
                                return false;
                            });
                            el.writeAttribute('title', Translator.translate('Out of Stock'));
                            el.select('span span').invoke('update', Translator.translate('Out of Stock'));
                        });
                    }
                };

                //execute setStockData again to reset the observers
                Product.ConfigurableSwatches.prototype.setStockData();
            }
        }
    }
}

function removeFormUrl(action){
    var qryInput = document.getElementById("qty");
    qryInput.form.action = action;
}

document.observe("dom:loaded", function() {
    overWriteConfigurableSwatches();
});

function toggleSearchTable(){
    if(document.getElementById('searchQuotes').style.display === 'none'){
        document.getElementById('filterQuote').innerHTML = '<a><label style="100px;">Hide Filter</label></a>';
        document.getElementById('searchQuotes').removeAttribute('style');
    }else{
        document.getElementById('filterQuote').innerHTML = '<a><label style="100px;">Show Filter</label></a>';
        document.getElementById('searchQuotes').style.display = 'none';
    }
}