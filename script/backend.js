;(function(jQuery) {

  jQuery(document).ready(function() {
    var tagBase = jQuery('#content_raonline_tableOfContents');
    if (tagBase.length > 0) {
      var tagNav = tagBase.find('#tabNav');
      var tagTabLinks = tagNav.children('li');
  
      tagSelected = tagTabLinks.filter('[active="yes"]');
      if (tagSelected.length < 1) { tagSelected = tagTabLinks.first(); }
      tagTabLinks.attr('active', 'no');
      tagSelected.attr('active', 'yes');
      tagTabLinks.on('click', function() {
        tagTabLinks.filter('[active="yes"]').attr('active', 'no');
        jQuery(this).attr('active', 'yes');
        tagTabContents.filter('[active="yes"]').attr('active', 'no');
        htmlID = jQuery(this).children('a').attr('href');
        tagTabContents.filter(htmlID).attr('active', 'yes');
        regExp = new RegExp("^#(.*)$");
        match = htmlID.match(regExp);
        tagBase.find('input[name="activeTab"]').val(match[1]);
        return false;
       });
      var tagContent = tagBase.find('#tabContent');
      var tagTabContents = tagContent.find('.tabContent');
      tagTabContents.attr('active', 'no');
      htmlID = tagSelected.children('a').attr('href');
      tagTabContents.filter(htmlID).attr('active', 'yes');
      regExp = new RegExp("^#(.*)$");
      match = htmlID.match(regExp);
      jQuery('<input type="hidden" name="activeTab" value="' + match[1] + '">').prependTo(tagBase.children('form'));
  
      var tagDependentFields = tagTabContents.find('[dependent]');
      tagDependentFields.each(function() {
        tagDependent = jQuery(this);
        regExp = new RegExp("^(.*)[:](.*)$");
        match = tagDependent.attr('dependent').match(regExp);
        if (typeof(match) != "undefined") {
          tagRelObject = jQuery('[name="' + match[1] + '"]');
          func1 = function(tagRelObject, tagDependent, match) {
            condition = false;
            if (tagRelObject.filter('[type="checkbox"]').length > 0) {
              if (tagRelObject.attr('checked') && (match[2] == 'on')) {
                condition = true;
               } else if (!tagRelObject.attr('checked') && (match[2] != 'on')) {
                condition = true;
               }
             } else {
              regExp = new RegExp("^(" + match[2] + ")$");
              if (tagRelObject.val().match(regExp)) {
                condition = true;
               }
             }
            if (condition === true) {
              tagDependent.show();
             } else {
              tagDependent.hide();
             }
           };
          tagRelObject.on('change', function() {
            tagRelObject = jQuery(this);
            tagDependentFields.each(function() {
              regExp = new RegExp("^(.*)[:](.*)$");
              match = jQuery(this).attr('dependent').match(regExp);
              if (match[1] === tagRelObject.attr('name')) {
                func1(tagRelObject, jQuery(this), match);
               }
             });
           });
          func1(tagRelObject, jQuery(this), match);
         }
       });
  
      var tagImageFields = tagTabContents.find('[image]');
      tagImageFields.each(function() {
        tagImageField = jQuery(this);
        tagImageBlock = jQuery('<div class="image" for="' + ( tagImageField.attr('id') ? tagImageField.attr('id') : tagImageField.attr('name') ) + '"></div>');
        tagImage = jQuery('<img class="thumbnail" src="' + tagImageField.attr('image').replace(/\{value\}/i, tagImageField.val()) + '">');
        tagImage.appendTo(tagImageBlock);
        tagImageBlock.insertAfter(tagImageField);
        tagImageField.on('change', function() {
          tagImageField = jQuery(this);
          tagImageBlock = tagImageField.parent().children('.image[for="' + ( tagImageField.attr('id') ? tagImageField.attr('id') : tagImageField.attr('name') ) + '"]');
          tagImage = tagImageBlock.children('img');
          tagImage.attr('src', tagImageField.attr('image').replace(/\{value\}/i, tagImageField.val()));
         });
        tagImageBlock.on('mouseover', function() {
          tagImageBlock = jQuery(this);
          tagImageBlock.css('width', tagImageBlock.css('width'));  
          tagImageBlock.css('height', tagImageBlock.css('height'));
          tagImage = tagImageBlock.children('img');
          tagImage.addClass('fullview').removeClass('thumbnail');
         });
        tagImageBlock.on('mouseout', function() {
          tagImageBlock = jQuery(this);
          tagImageBlock.css('width', tagImageBlock.css('width'));  
          tagImageBlock.css('height', tagImageBlock.css('height'));
          tagImage = tagImageBlock.children('img');
          tagImage.addClass('thumbnail').removeClass('fullview');
         });
        tagImageBlock.on('mousemove', function(e) {
          tagImageBlock = jQuery(this);
          tagImage = tagImageBlock.children('img');
          moveSectorA = { x: Math.max(tagImage.prop('naturalWidth') - tagImageBlock.width(), 0), y: Math.max(tagImage.prop('naturalHeight') - tagImageBlock.height(), 0) };
          moveSectorB = { x: (moveSectorA.x / tagImage.prop('naturalWidth')) * tagImage.width(), y: (moveSectorA.y / tagImage.prop('naturalHeight')) * tagImage.height() };
          posB = { x: (e.clientX - tagImageBlock.offset().left) / tagImageBlock.width(), y: (e.clientY - tagImageBlock.offset().top) / tagImageBlock.height() };
          tagImage.css('left', (-1) * posB.x * moveSectorB.x);
          tagImage.css('top', (-1) * posB.y * moveSectorB.y);
         });
       });
  
      tagContent.show();
      tagNav.show();
     }    

   });
  
 })(jQuery);