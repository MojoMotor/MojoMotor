/*
 * SimpleModal 1.4.1 - jQuery Plugin
 * http://www.ericmmartin.com/projects/simplemodal/
 * Copyright (c) 2010 Eric Martin (http://twitter.com/ericmmartin)
 * Dual licensed under the MIT and GPL licenses
 * Revision: $Id: jquery.simplemodal.js 261 2010-11-05 21:16:20Z emartin24 $
 */

(function(b){var j=b.browser.msie&&parseInt(b.browser.version)===6&&typeof window.XMLHttpRequest!=="object",l=b.browser.msie&&parseInt(b.browser.version)===7,k=null,e=[];b.modal=function(a,c){return b.modal.impl.init(a,c)};b.modal.close=function(){b.modal.impl.close()};b.modal.focus=function(a){b.modal.impl.focus(a)};b.modal.setContainerDimensions=function(){b.modal.impl.setContainerDimensions()};b.modal.setPosition=function(){b.modal.impl.setPosition()};b.modal.update=function(a,c){b.modal.impl.update(a,
c)};b.fn.modal=function(a){return b.modal.impl.init(this,a)};b.modal.defaults={appendTo:"body",focus:true,opacity:50,overlayId:"simplemodal-overlay",overlayCss:{},containerId:"simplemodal-container",containerCss:{},dataId:"simplemodal-data",dataCss:{},minHeight:null,minWidth:null,maxHeight:null,maxWidth:null,autoResize:false,autoPosition:true,zIndex:1E3,close:true,closeHTML:'<a class="modalCloseImg" title="Close"></a>',closeClass:"simplemodal-close",escClose:true,overlayClose:false,position:null,
persist:false,modal:true,onOpen:null,onShow:null,onClose:null};b.modal.impl={d:{},init:function(a,c){if(this.d.data)return false;k=b.browser.msie&&!b.boxModel;this.o=b.extend({},b.modal.defaults,c);this.zIndex=this.o.zIndex;this.occb=false;if(typeof a==="object"){a=a instanceof jQuery?a:b(a);this.d.placeholder=false;if(a.parent().parent().size()>0){a.before(b("<span></span>").attr("id","simplemodal-placeholder").css({display:"none"}));this.d.placeholder=true;this.display=a.css("display");if(!this.o.persist)this.d.orig=
a.clone(true)}}else if(typeof a==="string"||typeof a==="number")a=b("<div></div>").html(a);else{alert("SimpleModal Error: Unsupported data type: "+typeof a);return this}this.create(a);this.open();b.isFunction(this.o.onShow)&&this.o.onShow.apply(this,[this.d]);return this},create:function(a){e=this.getDimensions();if(this.o.modal&&j)this.d.iframe=b('<iframe src="javascript:false;"></iframe>').css(b.extend(this.o.iframeCss,{display:"none",opacity:0,position:"fixed",height:e[0],width:e[1],zIndex:this.o.zIndex,
top:0,left:0})).appendTo(this.o.appendTo);this.d.overlay=b("<div></div>").attr("id",this.o.overlayId).addClass("simplemodal-overlay").css(b.extend(this.o.overlayCss,{display:"none",opacity:this.o.opacity/100,height:this.o.modal?e[0]:0,width:this.o.modal?e[1]:0,position:"fixed",left:0,top:0,zIndex:this.o.zIndex+1})).appendTo(this.o.appendTo);this.d.container=b("<div></div>").attr("id",this.o.containerId).addClass("simplemodal-container").css(b.extend(this.o.containerCss,{display:"none",position:"fixed",
zIndex:this.o.zIndex+2})).append(this.o.close&&this.o.closeHTML?b(this.o.closeHTML).addClass(this.o.closeClass):"").appendTo(this.o.appendTo);this.d.wrap=b("<div></div>").attr("tabIndex",-1).addClass("simplemodal-wrap").css({height:"100%",outline:0,width:"100%"}).appendTo(this.d.container);this.d.data=a.attr("id",a.attr("id")||this.o.dataId).addClass("simplemodal-data").css(b.extend(this.o.dataCss,{display:"none"})).appendTo("body");this.setContainerDimensions();this.d.data.appendTo(this.d.wrap);
if(j||k)this.fixIE()},bindEvents:function(){var a=this;b("."+a.o.closeClass).bind("click.simplemodal",function(c){c.preventDefault();a.close()});a.o.modal&&a.o.close&&a.o.overlayClose&&a.d.overlay.bind("click.simplemodal",function(c){c.preventDefault();a.close()});b(document).bind("keydown.simplemodal",function(c){if(a.o.modal&&c.keyCode===9)a.watchTab(c);else if(a.o.close&&a.o.escClose&&c.keyCode===27){c.preventDefault();a.close()}});b(window).bind("resize.simplemodal",function(){e=a.getDimensions();
a.o.autoResize?a.setContainerDimensions():a.o.autoPosition&&a.setPosition();if(j||k)a.fixIE();else if(a.o.modal){a.d.iframe&&a.d.iframe.css({height:e[0],width:e[1]});a.d.overlay.css({height:e[0],width:e[1]})}})},unbindEvents:function(){b("."+this.o.closeClass).unbind("click.simplemodal");b(document).unbind("keydown.simplemodal");b(window).unbind("resize.simplemodal");this.d.overlay.unbind("click.simplemodal")},fixIE:function(){var a=this.o.position;b.each([this.d.iframe||null,!this.o.modal?null:this.d.overlay,
this.d.container],function(c,h){if(h){var g=h[0].style;g.position="absolute";if(c<2){g.removeExpression("height");g.removeExpression("width");g.setExpression("height",'document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight + "px"');g.setExpression("width",'document.body.scrollWidth > document.body.clientWidth ? document.body.scrollWidth : document.body.clientWidth + "px"')}else{var d,f;if(a&&a.constructor===Array){d=a[0]?typeof a[0]==="number"?
a[0].toString():a[0].replace(/px/,""):h.css("top").replace(/px/,"");d=d.indexOf("%")===-1?d+' + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"':parseInt(d.replace(/%/,""))+' * ((document.documentElement.clientHeight || document.body.clientHeight) / 100) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"';if(a[1]){f=typeof a[1]==="number"?a[1].toString():a[1].replace(/px/,"");
f=f.indexOf("%")===-1?f+' + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"':parseInt(f.replace(/%/,""))+' * ((document.documentElement.clientWidth || document.body.clientWidth) / 100) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"'}}else{d='(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (t = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"';
f='(document.documentElement.clientWidth || document.body.clientWidth) / 2 - (this.offsetWidth / 2) + (t = document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft) + "px"'}g.removeExpression("top");g.removeExpression("left");g.setExpression("top",d);g.setExpression("left",f)}}})},focus:function(a){var c=this;a=a&&b.inArray(a,["first","last"])!==-1?a:"first";var h=b(":input:enabled:visible:"+a,c.d.wrap);setTimeout(function(){h.length>0?h.focus():c.d.wrap.focus()},
10)},getDimensions:function(){var a=b(window);return[b.browser.opera&&b.browser.version>"9.5"&&b.fn.jquery<"1.3"||b.browser.opera&&b.browser.version<"9.5"&&b.fn.jquery>"1.2.6"?a[0].innerHeight:a.height(),a.width()]},getVal:function(a,c){return a?typeof a==="number"?a:a==="auto"?0:a.indexOf("%")>0?parseInt(a.replace(/%/,""))/100*(c==="h"?e[0]:e[1]):parseInt(a.replace(/px/,"")):null},update:function(a,c){if(!this.d.data)return false;this.d.origHeight=this.getVal(a,"h");this.d.origWidth=this.getVal(c,
"w");this.d.data.hide();a&&this.d.container.css("height",a);c&&this.d.container.css("width",c);this.setContainerDimensions();this.d.data.show();this.o.focus&&this.focus();this.unbindEvents();this.bindEvents()},setContainerDimensions:function(){var a=j||l,c=this.d.origHeight?this.d.origHeight:b.browser.opera?this.d.container.height():this.getVal(a?this.d.container[0].currentStyle.height:this.d.container.css("height"),"h");a=this.d.origWidth?this.d.origWidth:b.browser.opera?this.d.container.width():
this.getVal(a?this.d.container[0].currentStyle.width:this.d.container.css("width"),"w");var h=this.d.data.outerHeight(true),g=this.d.data.outerWidth(true);this.d.origHeight=this.d.origHeight||c;this.d.origWidth=this.d.origWidth||a;var d=this.o.maxHeight?this.getVal(this.o.maxHeight,"h"):null,f=this.o.maxWidth?this.getVal(this.o.maxWidth,"w"):null;d=d&&d<e[0]?d:e[0];f=f&&f<e[1]?f:e[1];var i=this.o.minHeight?this.getVal(this.o.minHeight,"h"):"auto";c=c?this.o.autoResize&&c>d?d:c<i?i:c:h?h>d?d:this.o.minHeight&&
i!=="auto"&&h<i?i:h:i;d=this.o.minWidth?this.getVal(this.o.minWidth,"w"):"auto";a=a?this.o.autoResize&&a>f?f:a<d?d:a:g?g>f?f:this.o.minWidth&&d!=="auto"&&g<d?d:g:d;this.d.container.css({height:c,width:a});this.d.wrap.css({overflow:h>c||g>a?"auto":"visible"});this.o.autoPosition&&this.setPosition()},setPosition:function(){var a,c;a=e[0]/2-this.d.container.outerHeight(true)/2;c=e[1]/2-this.d.container.outerWidth(true)/2;if(this.o.position&&Object.prototype.toString.call(this.o.position)==="[object Array]"){a=
this.o.position[0]||a;c=this.o.position[1]||c}else{a=a;c=c}this.d.container.css({left:c,top:a})},watchTab:function(a){if(b(a.target).parents(".simplemodal-container").length>0){this.inputs=b(":input:enabled:visible:first, :input:enabled:visible:last",this.d.data[0]);if(!a.shiftKey&&a.target===this.inputs[this.inputs.length-1]||a.shiftKey&&a.target===this.inputs[0]||this.inputs.length===0){a.preventDefault();this.focus(a.shiftKey?"last":"first")}}else{a.preventDefault();this.focus()}},open:function(){this.d.iframe&&
this.d.iframe.show();if(b.isFunction(this.o.onOpen))this.o.onOpen.apply(this,[this.d]);else{this.d.overlay.show();this.d.container.show();this.d.data.show()}this.o.focus&&this.focus();this.bindEvents()},close:function(){var a=this;if(!a.d.data)return false;a.unbindEvents();if(b.isFunction(a.o.onClose)&&!a.occb){a.occb=true;a.o.onClose.apply(a,[a.d])}else{if(a.d.placeholder){var c=b("#simplemodal-placeholder");if(a.o.persist)c.replaceWith(a.d.data.removeClass("simplemodal-data").css("display",a.display));
else{a.d.data.hide().remove();c.replaceWith(a.d.orig)}}else a.d.data.hide().remove();a.d.container.hide().remove();a.d.overlay.hide();a.d.iframe&&a.d.iframe.hide().remove();setTimeout(function(){a.d.overlay.remove();a.d={}},10)}}}})(jQuery);
