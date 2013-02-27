$(document).ready(function(){

	var main=$('#main');
	(function initScroll(){
		var selectMenu = $('.ui-selectmenu-menu'),
		i=0;
		function initSelectScroll(){
			selectMenu.eq(i).addClass('ui-selectmenu-open');
			$('.c', selectMenu.eq(i)).bind('jsp-initialised', function(){
				selectMenu.eq(i).removeClass('ui-selectmenu-open');
				i++;
				if (i<selectMenu.size()){
					initSelectScroll();
				}
			}).jScrollPane({
				verticalDragMinHeight: 39,
				verticalDragMaxHeight: 39
			});
		}
		initSelectScroll();
		$('.scrollable').jScrollPane({
			verticalDragMinHeight: 39,
			verticalDragMaxHeight: 39
		});
	})();
	(function quantitySwitch(){
		var holder=$('.up-down');
		holder.find('.up,.down').click(function(e){
			var input=$(this).parent().find('input'),
				val=+input.val();
			if($(this).hasClass('up')){
				input.val(++val);
			}else{
				if(val-1<1){
					input.val(1);
					$(this).addClass('disabled');
				}else{
					input.val(--val);
				}
			}
			e.preventDefault();
		});
		holder.find('input').blur(function(){
			$(this).val($(this).val().replace(/[^0-9]+/ig,''));
		});
	})();
	if("fancybox" in $.fn){
		(function fancybox(){
			$('.visual-col .visual>li .fancybox').fancybox({
				helpers:{
					thumbs:{
						width:50,
						height:50
					}
				}
			});
		})();
	}
	(function colHeight(){
		$('.two-box').each(function(){
			var height= 0;
			$(this).find('.col').each(function(){
				if($(this).outerHeight() > height){
					height=$(this).outerHeight();
				}
			});
			$(this).find('.col').outerHeight(height);
		})
	})();
	(function disable(){
		var firstRadio=$('.pay-list input:radio:first').get(0);
		$('.pay-list input:radio').change(function(){
			if(firstRadio!==this && $(this).is(':checked')){
				$(this).closest('.col').next().addClass('disabled');
			}else{
				$(this).closest('.col').next().removeClass('disabled');
			}
		}).trigger("change");
	})();
	(function tab(){
		$('.tab-wrapper').tabset();
	})();
	(function gallery(){
		$('.min-gallery').galleryScroll({
			stepWidth:376,
			fix:6,
			holderList: '.gallery-wrap'
		});
		$('.tab-gallery').galleryScroll();
		$('.promo-gallery').fadeGallery();
		$('.side-ads').fadeGallery();
		$('.visual-col').fadeGallery({
			autoRotation:false,
			pagerLinks:'.min-gallery li',
			slideElements:'.visual>li',
			btnNext:'',
			btnPrev:''
		});
	})();
	(function accordion(){
		$('.side-nav .opener').click(function(e){
			var li=$(this).parent(),
				slide=$(this).next();
				if(!li.parent().find('li .submenu:animated').size()){
					if(li.hasClass('active')){
						slide.slideUp(400,function(){
							li.removeClass('active');
						});
					}else{
						li.parent().find('li.active .submenu').slideUp(400,function(){
							$(this).parent().removeClass('active');
						});
						slide.slideDown(400,function(){
							li.addClass('active');
						});
					}
				}
				
			e.preventDefault();
		});
	})();
	(function readMore(){
		$('.comment-list>li').each(function(){
			var li=$(this),
				opener=$(this).find('.opener-more'),
				height=0;
			if(li.hasClass('active')){
				height=li.height();
			}else{
				li.addClass('active');
				height=li.height();
				li.removeClass('active');
			}
			opener.click(function(e){
					opener.hide();
					li.addClass('active');
					var height=li.height();
					li.height(118);
					li.animate({'height':height},400,function(){
						li.addClass('active');
					});
				e.preventDefault();
			});
		});
	})();
	(function openBasket(){
		var basket=$('.min-basket');
		$('.opener-view').click(function(e){
			basket.toggle().css({'top':main.offset().top,'left':main.offset().left+main.width()-basket.outerWidth()});
		});
		$(window).resize(function(){
			if(basket.is(':visible')){
				basket.css({'top':main.offset().top,'left':main.offset().left+main.width()-basket.outerWidth()});
			}
		});
		$(document).click(function(e){
			if(!$(e.target).closest('.min-basket').size() && !$(e.target).closest('.opener-view').size()){
				basket.hide();
			}
		});
	})();
	(function openAccount(){
		var accountDrop=$('.account-drop');
		$('.opener-account').click(function(e){
			accountDrop.toggle();
			e.preventDefault();
		});
		$(document).click(function(e){
			if(!$(e.target).closest('.account-drop').size() && !$(e.target).closest('.opener-account').size()){
				accountDrop.hide();
			}
		});
	})();
	(function popup(){
		$('body').popup({
			"opener":".opener-login",
			"popup_holder":"#enter-login",
			"popup":".login-popup",
			"close_btn":".close"
		});
	})();
	(function help(){
		var help=$('.side-help');
		$(window).on('resize.help',function(){
			help.css({'left':main.offset().left-help.outerWidth()-15});
			if(main.offset().left-help.outerWidth()>0 && help.is(':visible')){
				help.css('visibility','visible');
			}else{
				help.css('visibility','hidden');
			}
		}).trigger('resize.help');
	})();
	(function resizeContent(){
		var content=$('#content'),
			sidebar=$('#side-bar'),
			header=$('#header').outerHeight(true);
		if(content.outerHeight()<sidebar.height()){
			content.height(sidebar.height()+header+274);
		}
	})();
});
$.fn.tabset = function(o){
	var o = $.extend({
				"tab":".tab",
				"tab_control":".tab-set",
				"tab_control_parent":"",
				"tab_control_item":">li",
				"a_class":"active",
				"t_a_class":"active",
				"style": {
					"forActive": {"display":"block"},
					"forInActive": {"display":"none"}
				}
			},o);
	return this.each(function(){
		var tabset=$(this),
			tab=$(o.tab,tabset),
			ctrl_pnt=$(o.tab_control_parent,tabset),
			ctrl=$(o.tab_control,tabset).size() ? $(o.tab_control,tabset):$(o.tab_control,ctrl_pnt),
			ctrl_item=$(o.tab_control_item,ctrl),
			a_class={"name":o.a_class,"selector":"."+o.a_class},
			t_a_class={"name":o.t_a_class,"selector":"."+o.t_a_class},
			style=o.style;
			ctrl_item.click(function(e){
				var index=$(this).index(),
					curTab=tab.filter(t_a_class.selector).size() ? tab.filter(t_a_class.selector):tab.filter(':visible'),
					nextTab=tab.eq(index);
				$(this).parent().find(o.tab_control_item+a_class.selector).removeClass(a_class.name);
				$(this).addClass(a_class.name);
				if(style){
					curTab.css(style.forInActive).removeClass(t_a_class.name);
					nextTab.css(style.forActive).addClass(t_a_class.name);
				}else{
					curTab.removeClass(t_a_class.name);
					nextTab.addClass(t_a_class.name);
				}
				e.preventDefault();
			});
	});
}
jQuery.fn.galleryScroll = function(_options){
	var _options = jQuery.extend({
		btPrev: 'a.prev',
		btNext: 'a.next',
		holderList: '.tab-gallery-holder',
		scrollElParent: 'ul',
		scrollEl: 'li',
		slideNum: false,
		duration : 1000,
		step: false,
		circleSlide: true,
		disableClass: 'disable',
		funcOnclick: null,
		autoSlide:false,
		innerMargin:0,
		stepWidth:false,
		fix:0
	},_options);
	return this.each(function(){
		var _this = jQuery(this);
		var _holderBlock = jQuery(_options.holderList,_this);
		var _gWidth = _holderBlock.width();
		var _animatedBlock = jQuery(_options.scrollElParent,_holderBlock);
		var _liWidth = jQuery(_options.scrollEl,_animatedBlock).outerWidth(true);
		var _liSum = jQuery(_options.scrollEl,_animatedBlock).length * _liWidth;
		_liSum-=_options.fix;
		var _margin = -_options.innerMargin;
		var f = 0;
		var _step = 0;
		var _autoSlide = _options.autoSlide;
		var _timerSlide = null;
		if (!_options.step) _step = _gWidth; else _step = _options.step*_liWidth;
		if (_options.stepWidth) _step = _options.stepWidth;
		
		if (!_options.circleSlide) {
			if (_options.innerMargin == _margin)
				jQuery(_options.btPrev,_this).addClass('prev-'+_options.disableClass);
		}
		if (_options.slideNum && !_options.step) {
			var _lastSection = 0;
			var _sectionWidth = 0;
			while(_sectionWidth < _liSum)
			{
				_sectionWidth = _sectionWidth + _gWidth;
				if(_sectionWidth > _liSum) {
				       _lastSection = _sectionWidth - _liSum;
				}
			}
		}
		if (_autoSlide) {
				_timerSlide = setTimeout(function(){
					autoSlide(_autoSlide);
				}, _autoSlide);
			_animatedBlock.hover(function(){
				clearTimeout(_timerSlide);
			}, function(){
				_timerSlide = setTimeout(function(){
					autoSlide(_autoSlide)
				}, _autoSlide);
			});
		}
		jQuery(_options.btNext,_this).unbind('click');
		jQuery(_options.btPrev,_this).unbind('click');
		jQuery(_options.btNext,_this).bind('click',function(e){
			jQuery(_options.btPrev,_this).removeClass('prev-'+_options.disableClass);
			if (!_options.circleSlide) {
				if (_margin + _step  > _liSum - _gWidth - _options.innerMargin) {
					if (_margin != _liSum - _gWidth - _options.innerMargin) {
						_margin = _liSum - _gWidth  + _options.innerMargin;
						jQuery(_options.btNext,_this).addClass('next-'+_options.disableClass);
						_f2 = 0;
					} 
				} else {
					_margin = _margin + _step;
					if (_margin == _liSum - _gWidth - _options.innerMargin) {
						jQuery(_options.btNext,_this).addClass('next-'+_options.disableClass);_f2 = 0;
					} 					
				}
			} else {
				if (_margin + _step  > _liSum - _gWidth + _options.innerMargin) {
					if (_margin != _liSum - _gWidth + _options.innerMargin) {
						
						_margin = _liSum - _gWidth  + _options.innerMargin;
					} else {
						_f2 = 1;
						_margin = -_options.innerMargin;
						
					}
				} else {
					_margin = _margin + _step;
					_f2 = 0;
				}
			} 
			
			_animatedBlock.animate({marginLeft: -_margin+"px"}, {queue:false,duration: _options.duration });
			
			if (_timerSlide) {
				clearTimeout(_timerSlide);
				_timerSlide = setTimeout(function(){
					autoSlide(_options.autoSlide)
				}, _options.autoSlide);
			}
			
			if (_options.slideNum && !_options.step) jQuery.fn.galleryScroll.numListActive(_margin,jQuery(_options.slideNum, _this),_gWidth,_lastSection);
			if (jQuery.isFunction(_options.funcOnclick)) {
				_options.funcOnclick.apply(_this);
			}
			e.preventDefault();
		});
		var _f2 = 1;
		jQuery(_options.btPrev, _this).bind('click',function(e){
			jQuery(_options.btNext,_this).removeClass('next-'+_options.disableClass);
			if (_margin - _step >= -_step - _options.innerMargin && _margin - _step <= -_options.innerMargin) {
				if (_f2 != 1) {
					_margin = -_options.innerMargin;
					_f2 = 1;
				} else {
					if (_options.circleSlide) {
						_margin = _liSum - _gWidth  + _options.innerMargin;
						f=1;_f2=0;
					} else {
						_margin = -_options.innerMargin
					}
				}
			} else if (_margin - _step < -_step + _options.innerMargin) {
				_margin = _margin - _step;
				f=0;
			}
			else {_margin = _margin - _step;f=0;};
			
			if (!_options.circleSlide && _margin == _options.innerMargin) {
				jQuery(this).addClass('prev-'+_options.disableClass);
				_f2=0;
			}
			
			if (!_options.circleSlide && _margin == -_options.innerMargin) jQuery(this).addClass('prev-'+_options.disableClass);
			_animatedBlock.animate({marginLeft: -_margin + "px"}, {queue:false, duration: _options.duration});
			
			if (_options.slideNum && !_options.step) jQuery.fn.galleryScroll.numListActive(_margin,jQuery(_options.slideNum, _this),_gWidth,_lastSection);
			
			if (_timerSlide) {
				clearTimeout(_timerSlide);
				_timerSlide = setTimeout(function(){
					autoSlide(_options.autoSlide)
				}, _options.autoSlide);
			}
			
			if (jQuery.isFunction(_options.funcOnclick)) {
				_options.funcOnclick.apply(_this);
			}
			e.preventDefault();
		});
		
		if (_liSum <= _gWidth) {
			jQuery(_options.btPrev,_this).addClass('prev-'+_options.disableClass).unbind('click');
			jQuery(_options.btNext,_this).addClass('next-'+_options.disableClass).unbind('click');
		}
		// auto slide
		function autoSlide(autoSlideDuration){
			//if (_options.circleSlide) {
				jQuery(_options.btNext,_this).trigger('click');
			//}
		};
		// Number list
		jQuery.fn.galleryScroll.numListCreate = function(_elNumList, _liSumWidth, _width, _section){
			var _numListElC = '';
			var _num = 1;
			var _difference = _liSumWidth + _section;
			while(_difference > 0)
			{
				_numListElC += '<li><a href="">'+_num+'</a></li>';
				_num++;
				_difference = _difference - _width;
			}
			jQuery(_elNumList).html('<ul class="control">'+_numListElC+'</ul>');
		};
		jQuery.fn.galleryScroll.numListActive = function(_marginEl, _slideNum, _width, _section){
			if (_slideNum) {
				jQuery('a',_slideNum).removeClass('active');
				var _activeRange = _width - _section-1;
				var _n = 0;
				if (_marginEl != 0) {
					while (_marginEl > _activeRange) {
						_activeRange = (_n * _width) -_section-1 + _options.innerMargin;
						_n++;
					}
				}
				var _a  = (_activeRange+_section+1 + _options.innerMargin)/_width - 1;
				jQuery('a',_slideNum).eq(_a).addClass('active');
			}
		};
		if (_options.slideNum && !_options.step) {
			
			jQuery.fn.galleryScroll.numListCreate(jQuery(_options.slideNum, _this), _liSum, _gWidth,_lastSection);
			jQuery.fn.galleryScroll.numListActive(_margin, jQuery(_options.slideNum, _this),_gWidth,_lastSection);
			numClick();
		};
		function numClick() {
			jQuery(_options.slideNum, _this).find('a').click(function(e){
				jQuery(_options.btPrev,_this).removeClass('prev-'+_options.disableClass);
				jQuery(_options.btNext,_this).removeClass('next-'+_options.disableClass);
				
				var _indexNum = jQuery(_options.slideNum, _this).find('a').index(jQuery(this));
				_margin = (_step*_indexNum) - _options.innerMargin;
				f=0; _f2=0;
				if (_indexNum == 0) _f2=1;
				if (_margin + _step > _liSum) {
					_margin = _margin - (_margin - _liSum) - _step + _options.innerMargin;
					if (!_options.circleSlide) jQuery(_options.btNext, _this).addClass('next-'+_options.disableClass);
				}
				_animatedBlock.animate({marginLeft: -_margin + "px"}, {queue:false, duration: _options.duration});
				
				if (!_options.circleSlide && _margin==0) jQuery(_options.btPrev,_this).addClass('prev-'+_options.disableClass);
				jQuery.fn.galleryScroll.numListActive(_margin, jQuery(_options.slideNum, _this),_gWidth,_lastSection);
				
				if (_timerSlide) {
					clearTimeout(_timerSlide);
					_timerSlide = setTimeout(function(){
						autoSlide(_options.autoSlide)
					}, _options.autoSlide);
				}
				e.preventDefault();
			});
		};
		jQuery(window).resize(function(){
			_gWidth = _holderBlock.width();
			_liWidth = jQuery(_options.scrollEl,_animatedBlock).outerWidth(true);
			_liSum = jQuery(_options.scrollEl,_animatedBlock).length * _liWidth;
			if (!_options.step) _step = _gWidth; else _step = _options.step*_liWidth;
			if (_options.slideNum && !_options.step) {
				var _lastSection = 0;
				var _sectionWidth = 0;
				while(_sectionWidth < _liSum)
				{
					_sectionWidth = _sectionWidth + _gWidth;
					if(_sectionWidth > _liSum) {
					       _lastSection = _sectionWidth - _liSum;
					}
				};
			};
		});
	});
}
$.fn.popup = function(o){
	var o = $.extend({
				"opener":".call-back a",
				"popup_holder":"#call-popup",
				"popup":".popup",
				"close_btn":".close",
				"close":function(){},
				"beforeOpen":function(){}
			},o);
	return this.each(function(){
		var container=$(this),
			opener=$(o.opener,container),
			popup_holder=$(o.popup_holder,container),
			popup=$(o.popup,popup_holder),
			close=$(o.close_btn,popup),
			bg=$('.bg',popup_holder);
			popup.css('margin',0);
			opener.click(function(e){
				o.beforeOpen.apply(this,[popup_holder]);
				popup_holder.fadeIn(400);
				alignPopup();
				bgResize();
				e.preventDefault();
			});
		function alignPopup(){
				if((($(window).height() / 2) - (popup.outerHeight() / 2))+ $(window).scrollTop()<0){
					popup.css({'top':0,'left': (($(window).width() - popup.outerWidth())/2) + $(window).scrollLeft()});
					return false;
				}
				popup.css({
					'top': (($(window).height()-popup.outerHeight())/2) + $(window).scrollTop(),
					'left': (($(window).width() - popup.outerWidth())/2) + $(window).scrollLeft()
				});
		}
		function bgResize(){
			var _w=$(window).width(),
				_h=$(document).height();
			bg.css({"height":_h,"width":_w+$(window).scrollLeft()});
		}
		$(window).resize(function(){
			if(popup_holder.is(":visible")){
				bgResize();
				alignPopup();
			}
		});
		if(popup_holder.is(":visible")){
				bgResize();
				alignPopup();
		}
		close.add(bg).click(function(e){
			var closeEl=this;
			popup_holder.fadeOut(400,function(){
				o.close.apply(closeEl,[popup_holder]);
			});
			e.preventDefault();
		});
		$('body').keydown(function(e){
			if(e.keyCode=='27'){
				popup_holder.fadeOut(400);
			}
		})
	});
}
jQuery.fn.fadeGallery = function(_options){
	var _options = jQuery.extend({
		slideElements:'.list>li',
		pagerLinks:'.switcher li',
		btnNext:'a.next',
		btnPrev:'a.prev',
		btnPlayPause:'a.play-pause',
		pausedClass:'paused',
		playClass:'playing',
		activeClass:'active',
		pauseOnHover:true,
		autoRotation:true,
		autoHeight:false,
		switchTime:3000,
		duration:650,
		event:'click'
	},_options);

	return this.each(function(){
		var _this = jQuery(this);
		var _slides = jQuery(_options.slideElements, _this);
		var _pagerLinks = jQuery(_options.pagerLinks, _this);
		var _btnPrev = jQuery(_options.btnPrev, _this);
		var _btnNext = jQuery(_options.btnNext, _this);
		var _btnPlayPause = jQuery(_options.btnPlayPause, _this);
		var _pauseOnHover = _options.pauseOnHover;
		var _autoRotation = _options.autoRotation;
		var _activeClass = _options.activeClass;
		var _pausedClass = _options.pausedClass;
		var _playClass = _options.playClass;
		var _autoHeight = _options.autoHeight;
		var _duration = _options.duration;
		var _switchTime = _options.switchTime;
		var _controlEvent = _options.event;

		var _hover = false;
		var _prevIndex = 0;
		var _currentIndex = 0;
		var _slideCount = _slides.length;
		var _timer;
		if(!_slideCount) return;
		_slides.hide().eq(_currentIndex).show();
		if(_autoRotation) _this.removeClass(_pausedClass).addClass(_playClass);
		else _this.removeClass(_playClass).addClass(_pausedClass);

		if(_btnPrev.length) {
			_btnPrev.bind(_controlEvent,function(){
				prevSlide();
				return false;
			});
		}
		if(_btnNext.length) {
			_btnNext.bind(_controlEvent,function(){
				nextSlide();
				return false;
			});
		}
		_pagerLinks.first().addClass('active');
		function switchNum(){
			_this.find('.num .total').text(_pagerLinks.size());
			_this.find('.num .active').text(_pagerLinks.filter('.active').index()+1);
		}
		switchNum();
		if(_pagerLinks.length) {
			_pagerLinks.each(function(_ind){
				jQuery(this).bind(_controlEvent,function(){
					if(_currentIndex != _ind) {
						_prevIndex = _currentIndex;
						_currentIndex = _ind;
						switchSlide();
					}
					switchNum();
					return false;
				});
			});
		}

		if(_btnPlayPause.length) {
			_btnPlayPause.bind(_controlEvent,function(){
				if(_this.hasClass(_pausedClass)) {
					_this.removeClass(_pausedClass).addClass(_playClass);
					_autoRotation = true;
					autoSlide();
				} else {
					if(_timer) clearRequestTimeout(_timer);
					_this.removeClass(_playClass).addClass(_pausedClass);
				}
				return false;
			});
		}

		function prevSlide() {
			_prevIndex = _currentIndex;
			if(_currentIndex > 0) _currentIndex--;
			else _currentIndex = _slideCount-1;
			switchSlide();
		}
		function nextSlide() {
			_prevIndex = _currentIndex;
			if(_currentIndex < _slideCount-1) _currentIndex++;
			else _currentIndex = 0;
			switchSlide();
			
		}
		function refreshStatus() {
			if(_pagerLinks.length) _pagerLinks.removeClass(_activeClass).eq(_currentIndex).addClass(_activeClass);
			_slides.eq(_prevIndex).removeClass(_activeClass);
			_slides.eq(_currentIndex).addClass(_activeClass);
		}
		function switchSlide() {
			_slides.stop(true,true);
			_slides.eq(_prevIndex).fadeOut(_duration);
			_slides.eq(_currentIndex).fadeIn(_duration);
			refreshStatus();
			autoSlide();
		}

		function autoSlide() {
			if(!_autoRotation || _hover) return;
			if(_timer) clearRequestTimeout(_timer);
			_timer = requestTimeout(nextSlide,_switchTime+_duration);
		}
		if(_pauseOnHover) {
			_this.hover(function(){
				_hover = true;
				if(_timer) clearRequestTimeout(_timer);
			},function(){
				_hover = false;
				autoSlide();
			});
		}
		refreshStatus();
		autoSlide();
	});
}
/*
 * Drop in replace functions for setTimeout() & setInterval() that
 * make use of requestAnimationFrame() for performance where available
 * http://www.joelambert.co.uk
 *
 * Copyright 2011, Joe Lambert.
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

// requestAnimationFrame() shim by Paul Irish
// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
window.requestAnimFrame = (function() {
	return  window.requestAnimationFrame   ||
			window.webkitRequestAnimationFrame ||
			window.mozRequestAnimationFrame    ||
			window.oRequestAnimationFrame      ||
			window.msRequestAnimationFrame     ||
			function(/* function */ callback, /* DOMElement */ element){
				window.setTimeout(callback, 1000 / 60);
			};
})();
/**
 * Behaves the same as setTimeout except uses requestAnimationFrame() where possible for better performance
 * @param {function} fn The callback function
 * @param {int} delay The delay in milliseconds
 */

window.requestTimeout = function(fn, delay) {
	if( !window.requestAnimationFrame      	&&
		!window.webkitRequestAnimationFrame &&
		!window.mozRequestAnimationFrame    &&
		!window.oRequestAnimationFrame      &&
		!window.msRequestAnimationFrame)
			return window.setTimeout(fn, delay);

	var start = new Date().getTime(),
		handle = new Object();

	function loop(){
		var current = new Date().getTime(),
			delta = current - start;

		delta >= delay ? fn.call() : handle.value = requestAnimFrame(loop);
	};

	handle.value = requestAnimFrame(loop);
	return handle;
};

/**
 * Behaves the same as clearInterval except uses cancelRequestAnimationFrame() where possible for better performance
 * @param {int|object} fn The callback function
 */
window.clearRequestTimeout = function(handle) {
    window.cancelAnimationFrame ? window.cancelAnimationFrame(handle.value) :
    window.webkitCancelRequestAnimationFrame ? window.webkitCancelRequestAnimationFrame(handle.value)	:
    window.mozCancelRequestAnimationFrame ? window.mozCancelRequestAnimationFrame(handle.value) :
    window.oCancelRequestAnimationFrame	? window.oCancelRequestAnimationFrame(handle.value) :
    window.msCancelRequestAnimationFrame ? msCancelRequestAnimationFrame(handle.value) :
    clearTimeout(handle);
};
(function($){
    
    $.fn.autoResize = function(options) {
        
        // Just some abstracted details,
        // to make plugin users happy:
        var settings = $.extend({
            onResize : function(){},
            animate : false,
            animateDuration : 150,
            animateCallback : function(){},
            extraSpace : 20,
            limit: 1000
        }, options);
        
        // Only textarea's auto-resize:
        this.filter('textarea').each(function(){
            
                // Get rid of scrollbars and disable WebKit resizing:
            var textarea = $(this).css({resize:'none','overflow-y':'hidden'}),
            
                // Cache original height, for use later:
                origHeight = textarea.height(),
                
                // Need clone of textarea, hidden off screen:
                clone = (function(){
                    
                    // Properties which may effect space taken up by chracters:
                    var props = ['height','width','lineHeight','textDecoration','letterSpacing'],
                        propOb = {};
                        
                    // Create object of styles to apply:
                    $.each(props, function(i, prop){
                        propOb[prop] = textarea.css(prop);
                    });
                    
                    // Clone the actual textarea removing unique properties
                    // and insert before original textarea:
                    return textarea.clone().removeAttr('id').removeAttr('name').css({
                        position: 'absolute',
                        top: 0,
                        left: -9999
                    }).css(propOb).attr('tabIndex','-1').insertBefore(textarea);
					
                })(),
                lastScrollTop = null,
                updateSize = function() {
					
                    // Prepare the clone:
                    clone.height(0).val($(this).val()).scrollTop(10000);
					
                    // Find the height of text:
                    var scrollTop = Math.max(clone.scrollTop(), origHeight) + settings.extraSpace,
                        toChange = $(this).add(clone);
						
                    // Don't do anything if scrollTip hasen't changed:
                    if (lastScrollTop === scrollTop) { return; }
                    lastScrollTop = scrollTop;
					
                    // Check for limit:
                    if ( scrollTop >= settings.limit ) {
                        $(this).css('overflow-y','');
                        return;
                    }
                    // Fire off callback:
                    settings.onResize.call(this);
					
                    // Either animate or directly apply height:
                    settings.animate && textarea.css('display') === 'block' ?
                        toChange.stop().animate({height:scrollTop}, settings.animateDuration, settings.animateCallback)
                        : toChange.height(scrollTop);
                };
            
            // Bind namespaced handlers to appropriate events:
            textarea
                .unbind('.dynSiz')
                .bind('keyup.dynSiz', updateSize)
                .bind('keydown.dynSiz', updateSize)
                .bind('change.dynSiz', updateSize);
            
        });
        
        // Chain:
        return this;
        
    };
    
    $(function() {
        function split( val ) {
            return val.split( /,\s*/ );
        }
        function extractLast( term ) {
            return split( term ).pop();
        }
        var parameter;
        $( ".search_with_param" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            selected = $('#undefined-button .ui-selectmenu-status').html();
            
            $('.search-form select[class=select-search]  option').each(function(){
                       if(selected==$(this).html())
                           parameter=$(this).val();
                    });
            search_route = $(this).data("route");
            search_link = $(this).data("link");
            search_type_link = $(this).data("type-link");
            after_search_action = $(this).data("after-search");
        if ($(this).val().length == 0){
            if (search_type_link == 'input'){
                $(search_link).val("");
                if (after_search_action !== undefined)
                        $(after_search_action).trigger("click");                           
            }
        }
        if ( event.keyCode === $.ui.keyCode.TAB &&
        $( this ).data( "autocomplete" ).menu.active ) {
        event.preventDefault();
        }
        })
        .autocomplete({
            source: function( request, response ) {
                $.getJSON( "/ru/auto/"+search_route+".json", {
                    term: extractLast( request.term ), 
                    parameter: parameter
                }, response );
            },
           search: function() {
                var term = extractLast( this.value );
                if ( term.length < 1 ) {
                    return false;
                }
            },
            focus: function() {
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                terms.pop();
                terms.push( ui.item.label );
                this.value = terms;
                    $(search_link).val(ui.item.value);
                    $(after_search_action).trigger("click");                    
                return false;
            }
        });
    });
    
})(jQuery);