
//formstone core
var Formstone=this.Formstone=function(a,b,c){"use strict";function d(a){l.Plugins[a].initialized||(l.Plugins[a].methods._setup.call(c),l.Plugins[a].initialized=!0)}function e(a,b,c,d){var e,f={raw:{}};d=d||{};for(e in d)d.hasOwnProperty(e)&&("classes"===a?(f.raw[d[e]]=b+"-"+d[e],f[d[e]]="."+b+"-"+d[e]):(f.raw[e]=d[e],f[e]=d[e]+"."+b));for(e in c)c.hasOwnProperty(e)&&("classes"===a?(f.raw[e]=c[e].replace(/{ns}/g,b),f[e]=c[e].replace(/{ns}/g,"."+b)):(f.raw[e]=c[e].replace(/.{ns}/g,""),f[e]=c[e].replace(/{ns}/g,b)));return f}function f(){var a,b={transition:"transitionend",MozTransition:"transitionend",OTransition:"otransitionend",WebkitTransition:"webkitTransitionEnd"},d=["transition","-webkit-transition"],e={transform:"transform",MozTransform:"-moz-transform",OTransform:"-o-transform",msTransform:"-ms-transform",webkitTransform:"-webkit-transform"},f="transitionend",g="",h="",i=c.createElement("div");for(a in b)if(b.hasOwnProperty(a)&&a in i.style){f=b[a],l.support.transition=!0;break}n.transitionEnd=f+".{ns}";for(a in d)if(d.hasOwnProperty(a)&&d[a]in i.style){g=d[a];break}l.transition=g;for(a in e)if(e.hasOwnProperty(a)&&e[a]in i.style){l.support.transform=!0,h=e[a];break}l.transform=h}function g(){l.windowWidth=l.$window.width(),l.windowHeight=l.$window.height(),o=k.startTimer(o,p,h)}function h(){for(var a in l.ResizeHandlers)l.ResizeHandlers.hasOwnProperty(a)&&l.ResizeHandlers[a].callback.call(b,l.windowWidth,l.windowHeight)}function i(a,b){return parseInt(a.priority)-parseInt(b.priority)}var j=function(){this.Version="0.5.13",this.Plugins={},this.ResizeHandlers=[],this.window=b,this.$window=a(b),this.document=c,this.$document=a(c),this.$body=null,this.windowWidth=0,this.windowHeight=0,this.userAgent=b.navigator.userAgent||b.navigator.vendor||b.opera,this.isFirefox=/Firefox/i.test(this.userAgent),this.isChrome=/Chrome/i.test(this.userAgent),this.isSafari=/Safari/i.test(this.userAgent)&&!this.isChrome,this.isMobile=/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(this.userAgent),this.isFirefoxMobile=this.isFirefox&&this.isMobile,this.transform=null,this.transition=null,this.support={file:!!(b.File&&b.FileList&&b.FileReader),history:!!(b.history&&b.history.pushState&&b.history.replaceState),matchMedia:!(!b.matchMedia&&!b.msMatchMedia),raf:!(!b.requestAnimationFrame||!b.cancelAnimationFrame),touch:!!("ontouchstart"in b||b.DocumentTouch&&c instanceof b.DocumentTouch),transition:!1,transform:!1}},k={killEvent:function(a,b){try{a.preventDefault(),a.stopPropagation(),b&&a.stopImmediatePropagation()}catch(c){}},startTimer:function(a,b,c,d){return k.clearTimer(a),d?setInterval(c,b):setTimeout(c,b)},clearTimer:function(a,b){a&&(b?clearInterval(a):clearTimeout(a),a=null)},sortAsc:function(a,b){return parseInt(b)-parseInt(a)},sortDesc:function(a,b){return parseInt(b)-parseInt(a)}},l=new j,m={base:"{ns}",element:"{ns}-element"},n={namespace:".{ns}",blur:"blur.{ns}",change:"change.{ns}",click:"click.{ns}",dblClick:"dblclick.{ns}",drag:"drag.{ns}",dragEnd:"dragend.{ns}",dragEnter:"dragenter.{ns}",dragLeave:"dragleave.{ns}",dragOver:"dragover.{ns}",dragStart:"dragstart.{ns}",drop:"drop.{ns}",error:"error.{ns}",focus:"focus.{ns}",focusIn:"focusin.{ns}",focusOut:"focusout.{ns}",input:"input.{ns}",keyDown:"keydown.{ns}",keyPress:"keypress.{ns}",keyUp:"keyup.{ns}",load:"load.{ns}",mouseDown:"mousedown.{ns}",mouseEnter:"mouseenter.{ns}",mouseLeave:"mouseleave.{ns}",mouseMove:"mousemove.{ns}",mouseOut:"mouseout.{ns}",mouseOver:"mouseover.{ns}",mouseUp:"mouseup.{ns}",resize:"resize.{ns}",scroll:"scroll.{ns}",select:"select.{ns}",touchCancel:"touchcancel.{ns}",touchEnd:"touchend.{ns}",touchLeave:"touchleave.{ns}",touchMove:"touchmove.{ns}",touchStart:"touchstart.{ns}"};j.prototype.Plugin=function(c,f){return l.Plugins[c]=function(c,f){function g(b){var e="object"===a.type(b);b=a.extend(!0,{},f.defaults||{},e?b:{});for(var g=this,h=0,i=g.length;i>h;h++){var k=g.eq(h);if(!j(k)){var l=k.data(c+"-options"),m=a.extend(!0,{$el:k},b,"object"===a.type(l)?l:{});k.addClass(f.classes.raw.element).data(s,m),d(c),f.methods._construct.apply(k,[m].concat(Array.prototype.slice.call(arguments,e?1:0)))}}return g}function h(){f.functions.iterate.apply(this,[f.methods._destruct].concat(Array.prototype.slice.call(arguments,1))),this.removeClass(f.classes.raw.element).removeData(s)}function j(a){return a.data(s)}function o(b){if(this instanceof a){var c=f.methods[b];return"object"!==a.type(b)&&b?c&&0!==b.indexOf("_")?f.functions.iterate.apply(this,[c].concat(Array.prototype.slice.call(arguments,1))):this:g.apply(this,arguments)}}function p(c){var d=f.utilities[c]||f.utilities._initialize||!1;return d?d.apply(b,Array.prototype.slice.call(arguments,"object"===a.type(c)?0:1)):void 0}function q(b){f.defaults=a.extend(!0,f.defaults,b||{})}function r(b){for(var c=this,d=0,e=c.length;e>d;d++){var f=c.eq(d),g=j(f)||{};"undefined"!==a.type(g.$el)&&b.apply(f,[g].concat(Array.prototype.slice.call(arguments,1)))}return c}var s="fs-"+c;return f.initialized=!1,f.priority=f.priority||10,f.classes=e("classes",s,m,f.classes),f.events=e("events",c,n,f.events),f.functions=a.extend({getData:j,iterate:r},k,f.functions),f.methods=a.extend(!0,{_setup:a.noop,_construct:a.noop,_destruct:a.noop,_resize:!1,destroy:h},f.methods),f.utilities=a.extend(!0,{_initialize:!1,_delegate:!1,defaults:q},f.utilities),f.widget&&(a.fn[c]=o),a[c]=f.utilities._delegate||p,f.namespace=c,f.methods._resize&&(l.ResizeHandlers.push({namespace:c,priority:f.priority,callback:f.methods._resize}),l.ResizeHandlers.sort(i)),f}(c,f),l.Plugins[c]};var o=null,p=20;return l.$window.on("resize.fs",g),g(),a(function(){l.$body=a("body");for(var b in l.Plugins)l.Plugins.hasOwnProperty(b)&&d(b)}),n.clickTouchStart=n.click+" "+n.touchStart,f(),l}(jQuery,this,document);

!function(a){"function"==typeof define&&define.amd&&false?define(["jquery","./core"],a):a(jQuery,Formstone)}(function(a,b){"use strict";function c(a){if(b.support.file){var c="";a.label!==!1&&(c+='<div class="'+w.target+'">',c+=a.label,c+="</div>"),c+='<input class="'+w.input+'" type="file"',a.multiple&&(c+=" multiple"),a.accept&&(c+=' accept="'+a.accept+'"'),c+=">",a.baseClasses=[w.base,a.theme,a.customClass].join(" "),this.addClass(a.baseClasses).append(c),a.$input=this.find(v.input),a.queue=[],a.total=0,a.uploading=!1,a.disabled=!0,a.aborting=!1,this.on(x.click,v.target,a,i).on(x.dragEnter,a,m).on(x.dragOver,a,n).on(x.dragLeave,a,o).on(x.drop,a,p),a.$input.on(x.focus,a,j).on(x.blur,a,k).on(x.change,a,l),h.call(this,a)}}function d(a){b.support.file&&(a.$input.off(x.namespace),this.off(x.namespace).removeClass(a.baseClasses).html(""))}function e(b,c){var d;b.aborting=!0;for(var e in b.queue)b.queue.hasOwnProperty(e)&&(d=b.queue[e],("undefined"===a.type(c)||c>=0&&d.index===c)&&(d.started&&!d.complete?d.transfer.abort():f(b,d,"abort")));b.aborting=!1,s(b)}function f(a,b,c){b.error=!0,a.$el.trigger(x.fileError,[b,c]),a.aborting||s(a)}function g(a){a.disabled||(this.addClass(w.disabled),a.$input.prop("disabled",!0),a.disabled=!0)}function h(a){a.disabled&&(this.removeClass(w.disabled),a.$input.prop("disabled",!1),a.disabled=!1)}function i(a){y.killEvent(a);var b=a.data;b.disabled||b.$input.trigger(x.click)}function j(a){a.data.$el.addClass(w.focus)}function k(a){a.data.$el.removeClass(w.focus)}function l(a){y.killEvent(a);var b=a.data,c=b.$input[0].files;!b.disabled&&c.length&&q(b,c)}function m(a){y.killEvent(a);var b=a.data;b.$el.addClass(w.dropping).trigger(x.fileDragEnter)}function n(a){y.killEvent(a);var b=a.data;b.$el.addClass(w.dropping).trigger(x.fileDragOver)}function o(a){y.killEvent(a);var b=a.data;b.$el.removeClass(w.dropping).trigger(x.fileDragLeave)}function p(a){y.killEvent(a);var b=a.data,c=a.originalEvent.dataTransfer.files;b.$el.removeClass(w.dropping),b.disabled||q(b,c)}function q(a,b){for(var c=[],d=0;d<b.length;d++){var e={index:a.total++,file:b[d],name:b[d].name,size:b[d].size,started:!1,complete:!1,error:!1,transfer:null};c.push(e),a.queue.push(e)}a.$el.trigger(x.queued,[c]),a.autoUpload&&r(a),a.$input.val("")}function r(a){a.uploading||(z.on(x.beforeUnload,function(){return a.leave}),a.uploading=!0,a.$el.trigger(x.start,[a.queue])),s(a)}function s(a){var b=0,c=[];for(var d in a.queue)!a.queue.hasOwnProperty(d)||a.queue[d].complete||a.queue[d].error||c.push(a.queue[d]);a.queue=c;for(var e in a.queue)if(a.queue.hasOwnProperty(e)){if(!a.queue[e].started){var f=new FormData;f.append(a.postKey,a.queue[e].file);for(var g in a.postData)a.postData.hasOwnProperty(g)&&f.append(g,a.postData[g]);t(a,f,a.queue[e])}if(b++,b>=a.maxQueue)return;d++}0===b&&(z.off(x.beforeUnload),a.uploading=!1,a.$el.trigger(x.complete))}function t(b,c,d){c=b.beforeSend.call(b.$el,c,d),d.size>=b.maxSize||c===!1||d.error===!0?f(b,d,c?"size":"abort"):(d.started=!0,d.transfer=a.ajax({url:b.action,data:c,dataType:b.dataType,type:"POST",contentType:!1,processData:!1,cache:!1,xhr:function(){var c=a.ajaxSettings.xhr();return c.upload&&c.upload.addEventListener("progress",function(a){var c=0,e=a.loaded||a.position,f=a.total;a.lengthComputable&&(c=Math.ceil(e/f*100)),b.$el.trigger(x.fileProgress,[d,c])},!1),c},beforeSend:function(a,c){b.$el.trigger(x.fileStart,[d])},success:function(a,c,e){d.complete=!0,b.$el.trigger(x.fileComplete,[d,a]),s(b)},error:function(a,c,e){f(b,d,e)}}))}var u=b.Plugin("upload",{widget:!0,defaults:{accept:!1,action:"",autoUpload:!0,beforeSend:function(a){return a},customClass:"",dataType:"html",label:"Drag and drop files or click to select",leave:"You have uploads pending, are you sure you want to leave this page?",maxQueue:2,maxSize:5242880,multiple:!0,postData:{},postKey:"file",theme:"fs-light"},classes:["input","target","multiple","dropping","disabled","focus"],methods:{_construct:c,_destruct:d,disable:g,enable:h,abort:e,start:r}}),v=u.classes,w=v.raw,x=u.events,y=u.functions,z=(b.window,b.$window);x.complete="complete",x.fileComplete="filecomplete",x.fileDragEnter="filedragenter",x.fileDragLeave="filedragleave",x.fileDragOver="filedragover",x.fileError="fileerror",x.fileProgress="fileprogress",x.fileStart="filestart",x.start="start",x.queued="queued"});

//custom
function PCCFUploadOnStart(e, files) {
    //console.log("Start");
    var upload = jQuery(e.currentTarget).parents('.pcfileupload');
    var fileQueue = upload.find('.queue');
    var fileList = upload.find('.complete');
    var html = '';
    for (var i = 0; i < files.length; i++) {
        html += '<li data-index="' + files[i].index + '"><span class="file">' + files[i].name + '</span><span class="progress">' + files[i].name + '</span></li>';
    }
    fileQueue.append(html);
    var prnts = upload.parents('.pcform_bottom_left,.pcform_bottom_right');
    if(prnts.length){
        slideOutResize(prnts);
    }
    if(typeof jQuery.colorbox != 'undefined'){
        jQuery.colorbox.resize();
    }
}

function PCCFUploadOnComplete(e) {
    //console.log("Complete");
    // All done!

}

function PCCFUploadOnFileStart(e, file) {
    //console.log("File Start");
    var upload = jQuery(e.target).parents('.pcfileupload');
    var fileQueue = upload.find('.queue');
    fileQueue.find("li[data-index=" + file.index + "]")
        .find(".progress").css('width','2%');
}

function PCCFUploadOnFileProgress(e, file, percent) {
    //console.log("File Progress");
    var upload = jQuery(e.target).parents('.pcfileupload');
    var fileQueue = upload.find('.queue');
    fileQueue.find("li[data-index=" + file.index + "]")
        .find(".progress").css('width', percent + "%");
}

function PCCFUploadOnFileComplete(e, file, response) {
    //console.log(e);
    //console.log(file);
    //console.log(response);
    //console.log("File Complete");
    var upload = jQuery(e.target).parents('.pcfileupload');
    var fileQueue = upload.find('.queue');
    var fileList = upload.find('.complete');
    if (response.trim() === "" || response.toLowerCase().indexOf("error") > -1) {
        console.log('error');
        fileQueue.find("li[data-index=" + file.index + "]")
            .find('.progress').text(response)
            .css('width','100%')
            .css('background-color','#F59595');

        if(fileList.find('li').length == 0){
            //no successful uploads
            var fem = jQuery(e.target).find("input[type='file']");
            if(fem.hasClass('is-required')){
                fem.addClass('required-entry');
            }
            fem.prop('value',"");
        }

    } else {
        var target = fileQueue.find("li[data-index=" + file.index + "]");
        target.find(".file").text(file.name);
        target.find(".progress").css('width','100%').append('<img src="' + PCCBaseJSURL + 'contactforms/img/x.png" onclick="PCCFRemoveFile(\'' + file.name + '\',this)" />');
        target.appendTo(fileList);
        target.find(".progress").css('background-color','rgb(85, 213, 100)');

        //This is for making files selected on drag&drop.
        //Because Formstone doesn't make it selected on drag& drop.
        var fem = jQuery(e.target).find("input[type='file']");
        if(fem.hasClass('required-entry')){
            fem.addClass('is-required');
        }
        fem.removeClass("required-entry");
    }
    var prnts = upload.parents('.pcform_bottom_left,.pcform_bottom_right');
    if(prnts.length){
        slideOutResize(prnts);
    }
    if(typeof jQuery.colorbox != 'undefined'){
        jQuery.colorbox.resize();
    }
}

function PCCFUploadOnFileError(e, file, error) {
    //console.log("File Error");
    var upload = jQuery(e.target).parents('.pcfileupload');
    var fileQueue = upload.find('.queue');
    fileQueue.find("li[data-index=" + file.index + "]")
        .find('.progress').text(error)
        .css('width','100%')
        .css('background-color','#F59595');
}

function PCCFRemoveFile(fileName,em){
    //get parent en ids en shit
    var parentId = jQuery(em).closest('.pccf').attr('formid');
    var uniqId = jQuery(em).closest('.pccf').find('.pcc_uid').val();
    var index = $PC(em).parents('.pccf').find('.filelists').index($PC(em).closest('.filelists'));
    var url = PCCFileUploadUrl.slice(0,-7) + 'deleteupload/' + 'form_id/' + parentId + '/uid/' + uniqId + '/index/' + index + '/filename/' + fileName;
    $PC.get(url,function(data){
    	if(jQuery(em).closest('ul.filelist.complete').find("li").length==1){
            var fem = jQuery(em).closest(".pcfileupload").find("input[type='file']");
            if(fem.hasClass('is-required')){
                fem.addClass('required-entry');
                fem.prop('value',"");
            }
        }
        jQuery(em).closest('li').remove();
    });
}