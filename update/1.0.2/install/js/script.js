(function() {
    'use strict';

    if (!!window.AwzAutoUnp) {
        return;
    }
    window.AwzAutoUnp = function(options) {
        if(typeof options !== 'object') {
            throw new Error('options is not object');
        }
        if(!options.hasOwnProperty('node')) {
            throw new Error('options.node is required');
        }
        if(!options.hasOwnProperty('signedParameters')) {
            throw new Error('options.signedParameters is required');
        }
        this.signedParameters = options.signedParameters;
        this.node = options.node;
        this.ajaxTimer = (!!options.ajaxTimer ? options.ajaxTimer : false) || 100;
        this.debug = !!options.debug ? true : false;
		this.findDom();
        var parent = this;
        BX.addCustomEvent('onAjaxSuccess',function(sett, param){
            if(param.url.indexOf('/bitrix/tools/')>-1) return;
            parent.findDom();
        });
    };
    window.AwzAutoUnp.prototype = {
        allunp: [],
        find: function(el){
            if(!el) return;
            if(this.allunp.length>10) return;
            if(el.value.length==9){
                if(this.allunp.indexOf(el.value)>-1) return;
                this.allunp.push(el.value);
                var formData = {
                    'signed':this.signedParameters,
                    'method':'POST',
                    'unp':el.value
                };
                setTimeout(function(){
                    BX.ajax.runAction('awz:autounp.api.mnsrb.find', {
                        data: formData
                    }).then(function (response) {
                        try{
                            response.data.replace.forEach(function(item){
                                document.querySelectorAll(item[0]).forEach(function(field){
                                    if(field.type==='text'){
                                        field.value = item[1];
                                    }else if(field.type==='textarea'){
                                        field.value = item[1];
                                    }
                                });
                            });
                        }catch (e) {
                            console.log(e);
                        }
                    }, function (response) {});
                },this.ajaxTimer);
            }
        },
        findDom: function(node){
            if(!node) node = this.node;
            document.querySelectorAll(node).forEach(function(item){
                item.setAttribute('onkeyup','AwzAutoUnp_ob.find(this);');
            });
        }
    };
})();