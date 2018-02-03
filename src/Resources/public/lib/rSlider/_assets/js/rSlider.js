/*
  # ----- Script info:
  - Author: Michael Mammoliti
  - Name: rSlider.js
  - Version: 0.2.1
  - js dipendencies: jQuery
  - Release date: 8 February 2016
  - GitHub: https://github.com/MichaelMammoliti/rSlider.js

  # ----- Contact info
  - GitHub: https://github.com/MichaelMammoliti
  - Mail: mammoliti.michael@gmail.com
  - Twitter: @MichMammoliti

  # ----- License Info
  - Released under the GPL v3 license.
*/

(function(){

  var pluginName  = "rSlider",
      defaults    = {
        currentSlide: 0,
        defaultSlide: 0,
        delay: 7000,
        height: undefined,
        width: undefined,
        minHeight: 500,
        automatic: false,
        dirButtons: true,
        dirButtonNext: "next",
        dirButtonPrev: "prev",
        transitions: true
      };

  var Plugin = function(context, options)
  {

    // - DOM elements
    this.$context   = $(context);
    this.$view      = this.$context.find(".rSlider--view");
    this.$slides    = this.$view.find(".rSlider--slide");
    this.$images    = this.$slides.find(".rSlider--image");
    this.$container = this.$slides.find(".rSlider--container");
    this.$dotsControls  = this.$context.find(".rSlider--dots-controls");
    this.$arrowControls  = this.$context.find(".rSlider--arrow-controls");

    // - properties caching
    this.slidesLen    = this.$slides.length;

    // - settings
    this.settings = $.extend(defaults, options);

    // - Style caching
    this.w = this.settings.width || this.$context.width();
    this.h = this.settings.height || this.$context.height();

    // - Timers
    this.delayTimer = undefined;
    this.resizeTimer  = undefined;

    // - Init
    this.init();
  };

  var pluginProto = {

    init: function()
    {
      var self = this;

      if(self.settings.currentSlide !== self.settings.defaultSlide)
        self.setSlide(self.settings.defaultSlide)

      self.events();
      self.setStyle();
      self.startAutomaticMovement();
      self.setDotsControls();
      self.setArrowControls();
      self.activateButton( self.settings.currentSlide );
      self.fixSlideHeight();
      self.moveSlide();
      self.setAnimations();
    },

    calculateMargin: function()
    {
      var self    = this,
          margin  = -self.settings.currentSlide * self.w;

      return margin;
    },

    startAutomaticMovement: function()
    {
      var self    = this,
          moving  = function()
          {
            self.goToSlide(self.nextSlide());
            self.activateButton();
            self.moveSlide();
          };

      if(self.settings.automatic)
      {
        clearInterval(self.delayTimer);

        self.delayTimer = setInterval(moving, self.settings.delay)
      }
    },

    stopAutomaticMovement: function()
    {
      var self = this;

      clearInterval(self.delayTimer);
    },

    setStyle: function()
    {
      var self = this;

      self.setMetrics();
      self.setBackgroundImages();
    },

    setBackgroundImages: function()
    {
      var self = this,
          $imgs = self.$images.find("img"),
          assignAttribute = function()
          {
            var $img    = $(this),
                $parent = $img.parent(),
                attr    = $img.attr("src");

            $parent.css({"background-image": "url('" + attr + "')"});
          }

      $.each($imgs, assignAttribute);

      $imgs.remove();
    },

    setDotsControls: function()
    {
      var self = this,
          buttons = "",
          i = 0;

      $.each(self.$images, function()
      {
        buttons += "<button data-slide-index='" + i + "'></button>";
        i++;
      });

      self.$dotsControls.append(buttons);
    },

    setArrowControls: function()
    {
      var self = this,
          buttons = "";

      if(!self.settings.dirButtons) return;
      buttons += "<span><button data-dir='prev'>" + self.settings.dirButtonPrev + "</button></span>";
      buttons += "<span><button data-dir='next'>" + self.settings.dirButtonNext + "</button></span>";

      self.$arrowControls.append(buttons);
    },

    setMetrics: function()
    {
      var self = this;

      self.$slides.width(self.w);

      if(self.settings.height && self.settings.width)
      {
        self.$view.height(self.h);
        self.$context.width(self.w);
      }
    },

    nextSlide: function()
    {
      var self = this,
          index;

      index = self.settings.currentSlide+1;

      if(self.settings.currentSlide === self.slidesLen - 1) index = 0;

      return index;
    },

    prevSlide: function()
    {
      var self  = this,
          index = self.settings.currentSlide-1;

      if(self.settings.currentSlide === 0) index = self.slidesLen-1;

      return index;
    },

    setSlide: function(index)
    {
      var self = this;

      self.settings.currentSlide = index;

      return index;
    },

    moveSlide: function()
    {
      var self = this;

      self.$view.css({ "margin-left": self.calculateMargin() })
    },

    goToSlide: function(slideIndex)
    {
      var self  = this,
          index = slideIndex;

      // next or prev
      switch(index)
      {
        case "next":
          index = self.nextSlide();
          break;

        case "prev":
          index = self.prevSlide();
          break;
      };

      self.setSlide(index);
      self.fixSlideHeight();

      return self.settings.currentSlide;
    },

    activateButton: function(index)
    {
      var self    = this,
          buttons = self.$dotsControls.children("button"),
          index   = index || self.settings.currentSlide;

      buttons.removeClass('active');
      buttons.eq(index).addClass('active');
    },

    resizeImages: function(containerWidth)
    {
      var self = this;

      self.w = containerWidth;

      self.moveSlide();
      self.$slides.width(containerWidth);
    },


    // USER EVENTS
    userEvents: {
      handleDotsControls: function($btn)
      {
        var self  = this,
            dir   = $btn.data("slide-index");

        self.goToSlide(dir);
        self.startAutomaticMovement();
        self.activateButton();
        self.moveSlide();

        return self.settings.currentSlide;
      },

      handleArrowControls: function($btn)
      {
        var self  = this,
            dir   = $btn.data("dir");

        self.goToSlide(dir);
        self.startAutomaticMovement();
        self.activateButton();
        self.moveSlide();

        return self.settings.currentSlide;
      },

      resizeWindow: function()
      {
        var self  = this,
            w     = self.$context.innerWidth();

        self.resizeImages(w);
        self.fixSlideHeight();
        self.removeAnimations();
      }
    },

    fixSlideHeight: function()
    {
      var self      = this,
          numSlide  = self.settings.currentSlide,
          $slide    = self.$slides.eq(numSlide),
          $image    = $slide.find(".rSlider--image"),
          h         = $slide.find(".rSlider--container").outerHeight(),
          minH      = self.settings.minHeight;

      if( h < minH ) h = minH;
      if( h > self.$context.outerHeight() ) h = h;

      self.$slides.height(h);
      $slide.height(h);

      return h;
    },

    removeAnimations: function()
    {
      var self = this,
          className = "css-transitions";

      self.$context.removeClass(className);

      clearTimeout(self.resizeTimer);
      self.resizeTimer = setTimeout(self.setAnimations.bind(self), 500);
    },

    setAnimations: function()
    {
      var self = this,
          className = "css-transitions";

      if(!self.settings.transitions) return;

      self.$context.addClass(className);
    },

    events: function()
    {
      var self = this;

      // - Dot Controls
      self.$dotsControls.on("click", "button", function()
      {
        var $btn = $(this);
        self.userEvents.handleDotsControls.call(self, $btn);
      });

      // - Arrow Controls
      self.$arrowControls.on("click", "span", function()
      {
        var $btn = $(this).children("button");

        self.userEvents.handleArrowControls.call(self, $btn);
      });

      // - Window
      $(window).on("resize", self.userEvents.resizeWindow.bind(self));

      // - Slider timers
      self.$context.on( "mouseover", self.stopAutomaticMovement.bind(self) );
      self.$context.on( "mouseleave", self.startAutomaticMovement.bind(self));
    }

  };



  $.extend(Plugin.prototype, pluginProto);



  $.fn[pluginName] = function(options)
  {
    return $.each($(this), function()
    {
      return new Plugin( this, options);
    });
  };

  $(".rSlider").rSlider();

}());