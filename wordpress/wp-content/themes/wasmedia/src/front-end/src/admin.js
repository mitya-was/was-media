require('./scss/admin.scss');

const Typo = require('./typo');
const tingle = require('tingle.js/src/tingle.js');
const jQuery = window.jQuery;
const acf = window.acf;
const ajaxurl = window.ajaxurl;
const tagBox = window.tagBox;

let TagsModal = false;
let TagsCache = false;
let PublishButton = document.getElementById('publish') || false;
let PostType = document.querySelector('#post_type');

if (PublishButton && PostType && !~['banner', 'mailer'].indexOf(PostType.value)) {
  let ABCN = document.querySelectorAll('.abcn_');

  if (ABCN) {
    ABCN.forEach(i => i.addEventListener('click', was_custom_navigation, false));

    jQuery(document).ready(function() {
      acf.add_action('append', rebuild_navigation_content_editor);
      acf.add_action('sortstop', rebuild_navigation_content_editor);
      acf.add_action('remove', rebuild_navigation_content_editor);
      acf.add_action('load', rebuild_navigation_content_editor);
    });
  }

  PublishButton.addEventListener('click', was_check_required, false);
}

function workspaceController() {
  const wpAdmin = document.querySelector('.wp-admin'),
    menuCollapser = document.getElementById('wp-admin-bar-admin_was_collapser');
  if (!menuCollapser || typeof menuCollapser === 'undefined') {
    return;
  }
  const trigger = {
      minimizer: menuCollapser.querySelector('#wp-admin-bar-admin_was_minimize a'),
      maximizer: menuCollapser.querySelector('#wp-admin-bar-admin_was_maximize a')
    },
    target = {
      menuCollapser: document.getElementById('collapse-button'),
      columnRadio: {
        1: document.querySelector('.columns-prefs-1'),
        2: document.querySelector('.columns-prefs-2')
      }
    };

  const { minimizer, maximizer } = trigger;

  minimizer.onclick = function(event) {
    event.preventDefault();
    let condition =
      !wpAdmin.classList.contains('sticky-menu') && Typo.isElement(target.menuCollapser),
      checkboxIndex = 1;
    changeWorkspace({ condition, checkboxIndex });
  };

  maximizer.onclick = function(event) {
    event.preventDefault();
    let condition =
      wpAdmin.classList.contains('sticky-menu') && Typo.isElement(target.menuCollapser),
      checkboxIndex = 2;
    changeWorkspace({ condition, checkboxIndex });
  };

  function changeWorkspace(options) {
    let { condition, checkboxIndex } = options,
      { menuCollapser, columnRadio } = target,
      checkbox = columnRadio[checkboxIndex];
    condition && menuCollapser.click();
    Typo.isElement(checkbox) && checkbox.click();
  }
}

function was_custom_navigation(event) {
  event.preventDefault();

  let adminElement = document.querySelector(event.target.dataset.abcnGo);

  scrollAndBlink(adminElement);
}

function scrollAndBlink(element, parentOffset = false) {
  element.style.display = 'block';
  element.classList.add('abcn_start_blink');

  let offsetParent = parentOffset ? parentOffset : 0;

  setTimeout(function() {
    jQuery(document)
      .find('html, body')
      .animate(
        {
          scrollTop: element.offsetTop - 100 - offsetParent
        },
        {
          duration: 200,
          complete: function () {
            element.classList.add('abcn_blink');

            setTimeout(function () {
              element.classList.remove('abcn_blink');
              element.classList.remove('abcn_start_blink');
            }, 400);
          }
        }
      );
  }, 200);
}

function rebuild_navigation_content_editor() {
  setTimeout(function() {
    let menuContainer = jQuery('#contenteditor');
    let topChild = jQuery('#wp-admin-bar-admin_was_custom_nav-content-editor-items');
    let navContainer = jQuery('#wp-admin-bar-admin_was_custom_nav-content-editor-default');
    let layoutItems = menuContainer.find('.acf-input .acf-flexible-content .values .layout');

    topChild.siblings('li').remove();

    layoutItems.each(function(i, e) {
      navContainer.append(
        jQuery('<li/>', {
          class: 'wp-admin-bar-admin _was_custom_acf_nav_',
          'data-abcn-go': jQuery(e).data('id')
        }).append(
          jQuery('<a/>', {
            class: 'ab-item',
            href: '#'
          }).append(
            jQuery('<span/>', {
              text: jQuery(e)
                .find('div.acf-fc-layout-handle')
                .text()
            })
          )
        )
      );
    });
  }, 500);
}

/**
 * @return {boolean}
 */
function was_check_required(event) {
  let isPublishing = document.getElementById('visibility-radio-public').checked;

  if (isPublishing) {
    this.getGoToButton = function(name, target, path) {
      return jQuery('<a>', {
        class: 'was-required-error-go-to',
        'data-target': target,
        'data-path': path ? path : '',
        text: name,
        href: '#',
        css: {
          margin: '0 10px 0 10px'
        }
      }).prop('outerHTML');
    };

    let error = '';
    let breakLine = '<br/>';

    let PostFeatureImage = document.getElementById('_thumbnail_id');

    let PostTitle = document.getElementById('title');

    let SELTitleTag = document.getElementById('snippet-editor-title');
    let SELMetaDesc = document.getElementById('snippet-editor-meta-description');

    let PostTagsList = document.getElementById('post_tag').querySelector('.tagchecklist');

    if (PostFeatureImage.value === '-1') {
      error += `Вам нужно выбрать Featured Image перед публикацией матерала. ${this.getGoToButton(
        'Установить Featured Image!',
        'set-post-thumbnail'
      )} ${breakLine}`;
    }

    if (SELTitleTag && SELMetaDesc) {
      let SEOSeparatorElem = document.getElementById('yoast_seo_separator');
      let SEOSeparator =
        SEOSeparatorElem && SEOSeparatorElem.innerText !== '' ? SEOSeparatorElem.innerText : '-';
      let SEOPath = [
        '#traffic_light',
        '.wpseo_tab.wpseo_keyword_tab',
        '.snippet-editor__button.snippet-editor__edit-button'
      ];

      let SEOTitleMIN = SELTitleTag.value.indexOf(` ${SEOSeparator} WAS`) !== -1 ? 70 : 64;
      let SEOTitleMAX = SELTitleTag.value.indexOf(` ${SEOSeparator} WAS`) !== -1 ? 100 : 94;

      let SEODescMIN = 160;
      let SEODescMAX = 300;

      if (PostTitle.value.trim() === '') {
        error += `Заглавие поста не может быть пустым. ${this.getGoToButton(
          'Заполнить заглавие!',
          'title'
        )} ${breakLine}`;
      }

      if (
        SELTitleTag.value.trim() === '' &&
        PostTitle.value.trim() !== '' &&
        (PostTitle.value.trim().length < SEOTitleMIN || PostTitle.value.trim().length > SEOTitleMAX)
      ) {
        error += `Поле 'SEO title' должно содержать от ${SEOTitleMIN} до ${SEOTitleMAX} символов. ${this.getGoToButton(
          'Изменить SEO заглавие!',
          'snippet-editor-title',
          SEOPath
        )} ${breakLine}`;
      }

      if (
        SELTitleTag.value.trim() !== '' &&
        (SELTitleTag.value.trim().length < SEOTitleMIN ||
          SELTitleTag.value.trim().length > SEOTitleMAX)
      ) {
        error += `Поле 'SEO title' должно содержать от ${SEOTitleMIN} до ${SEOTitleMAX} символов. ${this.getGoToButton(
          'Изменить SEO заглавие!',
          'snippet-editor-title',
          SEOPath
        )} ${breakLine}`;
      }

      if (SELMetaDesc.value.trim() === '') {
        error += `Вам нужно заполнить поле 'Meta description' в Yoast SEO. ${this.getGoToButton(
          'Написать SEO описание!',
          'snippet-editor-meta-description',
          SEOPath
        )} ${breakLine}`;
      }

      if (
        SELMetaDesc.value.trim() !== '' &&
        (SELMetaDesc.value.trim().length < SEODescMIN ||
          SELMetaDesc.value.trim().length > SEODescMAX)
      ) {
        error += `Поле 'Meta description' должно содержать от ${SEODescMIN} до ${SEODescMAX} символов. ${this.getGoToButton(
          'Изменить SEO описание!',
          'snippet-editor-meta-description',
          SEOPath
        )} ${breakLine}`;
      }
    }

    let TagsCountMIN = 5;
    let TagPath = ['#link-post_tag'];

    if (PostTagsList.children.length < TagsCountMIN) {
      error += `Поле 'Tags' должно содержать минимум ${TagsCountMIN} тегов. ${this.getGoToButton(
        'Добавить тегов!',
        'new-tag-post_tag',
        TagPath
      )} ${breakLine}`;
    }

    if (error !== '') {
      event.preventDefault();

      let modal = new tingle.modal({
        footer: false,
        stickyFooter: true,
        closeMethods: ['overlay', 'button', 'escape'],
        closeLabel: '',
        cssClass: ['modal-content']
      });

      modal.setContent(error);
      modal.open();

      modal.modalBox.children[0].addEventListener('click', function(event) {
        event.preventDefault();

        let parent = event.target;

        if (parent.classList.contains('was-required-error-go-to')) {
          let target = jQuery(parent).attr('data-target');
          let path = jQuery(parent).attr('data-path');
          let targetElement = jQuery('#' + target);
          let timer = 100;

          if (path) {
            timer = 200;

            let elements = path.split(',');

            for (let i = 0; i < elements.length; i++) {
              if (jQuery(elements[i]).attr('aria-expanded') === 'true') {
                continue;
              }

              jQuery(elements[i])
                .delay(10)
                .trigger('click');
            }
          }

          setTimeout(function() {
            jQuery(document)
              .find('html, body')
              .animate(
                {
                  scrollTop: targetElement.offset().top - 100
                },
                200
              );

            setTimeout(function() {
              jQuery('.tingle-modal').remove();
              targetElement.click().focus();
            }, 210);
          }, timer);
        }
      });

      return false;
    }
  }
}

jQuery(document).on(
  'click',
  '#was-required-error-modal, #was-required-error-modal > .close, #was-helper-modal, #was-helper-modal > .close',
  function() {
    jQuery('#was-required-error-modal, #was-helper-modal').remove();
  }
);

let ResetButton = document.getElementById('was_reset_game') || false;

if (ResetButton) {
  ResetButton.addEventListener('click', init_was_reset, false);
}

function init_was_reset() {
  let postID = document.getElementById('post_ID');
  let request = new XMLHttpRequest();
  let resetStatusText = '';

  request.open('POST', '/wp-admin/admin-ajax.php?action=reset_game_stats', true);
  request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  request.send('post_id=' + postID.value);
  request.onreadystatechange = () => {
    if (request.readyState === XMLHttpRequest.DONE) {
      let messageHolder = document.getElementById('was_reset_game_message');
      let data = JSON.parse(request.responseText);

      if (data.success) {
        resetStatusText = 'Game Statistics Dropped.';
        messageHolder.style.color = 'green';
      } else {
        resetStatusText = 'Error While Dropping Game Statistics.';
        messageHolder.style.color = 'red';
      }

      messageHolder.innerText = resetStatusText;

      setTimeout(function() {
        messageHolder.innerText = '';
      }, 2000);
    }
  };
}

jQuery(document).ready(function() {
  let SEOForm = jQuery('#snippet_preview') || false;
  let SEOSeparatorElem = jQuery('#yoast_seo_separator');
  let SEOSeparator =
    SEOSeparatorElem && SEOSeparatorElem.text() !== '' ? SEOSeparatorElem.text() : '-';

  if (SEOForm.length > 0) {
    let SEOTitleElem = SEOForm.find('#snippet-editor-title');
    let SEODescriptionElem = SEOForm.find('#snippet-editor-meta-description');

    [SEOTitleElem, SEODescriptionElem].forEach(function(elem, count) {
      let SEOElemCurrentChars = elem.val().length;
      let SEOElemMaxChars =
        count === 0 ? (elem.val().indexOf(` ${SEOSeparator} WAS`) !== -1 ? 100 : 94) : 300;

      let SEOCounter = jQuery('<div>', {
        class: 'was-custom-seo-counter-wrapper',
        css: {
          display: 'inline-block'
        }
      }).append([
        jQuery('<span>', {
          text: ' - '
        }),
        jQuery('<span>', {
          html:
            '(<span class="was-custom-seo-counter-current">' +
            SEOElemCurrentChars +
            '</span> / <span class="was-custom-seo-counter-max">' +
            SEOElemMaxChars +
            '</span>)'
        })
      ]);

      elem.before(SEOCounter);
    });

    jQuery(document).on('input focus', '#snippet-editor-title', function (event) {
      wasCustomCounterCheck(jQuery(this), SEOSeparator, event.target.id);
    });

    jQuery(document).on('input focus', '#snippet-editor-meta-description', function (event) {
      wasCustomCounterCheck(jQuery(this), SEOSeparator, event.target.id);
    });
  }

  let YoastOTitle = jQuery('#yoast_wpseo_opengraph-title');
  let YoastTTitle = jQuery('#yoast_wpseo_twitter-title');

  if (YoastOTitle.val() === '') {
    YoastOTitle.val(jQuery('#title').val());
  }

  if (YoastTTitle.val() === '') {
    YoastTTitle.val(jQuery('#title').val());
  }

  jQuery(document).on('input', '#title', function () {
    jQuery('#yoast_wpseo_opengraph-title, #yoast_wpseo_twitter-title').val(jQuery(this).val());
  });

  let YoastODescription = jQuery('#yoast_wpseo_opengraph-description');
  let YoastTDescription = jQuery('#yoast_wpseo_twitter-description');

  if (YoastODescription.val() === '') {
    YoastODescription.val(jQuery('#excerpt').val());
  }

  if (YoastTDescription.val() === '') {
    YoastTDescription.val(jQuery('#excerpt').val());
  }

  jQuery(document).on('input', '#excerpt', function () {
    jQuery('#yoast_wpseo_opengraph-description, #yoast_wpseo_twitter-description').val(
      jQuery(this).val()
    );
  });

  // ======================== Tags Box ========================
  let tagBoxContainer = jQuery('#tagsdiv-post_tag, #tagsdiv-micro_tag');

  tagBoxContainer.find('h2.hndle.ui-sortable-handle').append(
    jQuery('<div>', {
      id: 'custom-tags-helper',
      class: 'dashicons dashicons-editor-help',
      css: {
        cursor: 'pointer',
        position: 'relative',
        display: 'inline-block',
        top: '2px',
        float: 'right',
        width: '50%'
      }
    }).append(
      jQuery('<span>', {
        text: ' Помощь',
        css: {
          fontSize: '14px',
          position: 'relative',
          top: '-5px'
        }
      })
    )
  );

  tagBoxContainer.find('#post_tag, #micro_tag').after(
    jQuery('<a>', {
      id: 'show-was-tags-list',
      href: '/',
      text: 'Выбрать из всех тегов постов',
      css: {
        fontSize: '14px',
        position: 'relative',
        top: '-5px'
      }
    })
  );

  // ======================== Modals ========================
  jQuery('#show-was-tags-list').on('click', function (event) {
    event.preventDefault();

    if (TagsModal) {
      TagsModal.open();

      return false;
    }

    TagsModal = new tingle.modal({
      footer: false,
      stickyFooter: true,
      closeMethods: ['overlay', 'button', 'escape'],
      closeLabel: '',
      cssClass: ['allTags-modal']
    });

    let loaderContainer = jQuery('<div>', {class: 'kart-loader kart-loader-invert'}).append([
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'})),
      jQuery('<div>', {class: 'sheath'}).append(jQuery('<div>', {class: 'segment'}))
    ]);

    TagsModal.setContent(loaderContainer.prop('outerHTML'));
    TagsModal.open();

    if (!TagsCache) {
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl + '?action=get_was_tags_list',

        success: function(data) {
          data = JSON.parse(data);

          if (data.success) {
            TagsCache = data.data;

            generateTagsList(data.data);
          } else {
            showTagsError(data.error);
          }
        },

        error: function() {
          jQuery('.allTags-modal')
            .find('.kart-loader')
            .hide();

          showTagsError();
        }
      });
    } else {
      generateTagsList(TagsCache);
    }

    event.stopPropagation();
  });

  jQuery('#custom-tags-helper').on('click', function (event) {
    event.preventDefault();

    let text = '<ol class="custom-tags-helper_modal">';

    text += '<li>Для тегов можно использовать любые прилагательные и существительные.</li>';
    text += '<li>Теги желательно по возможности формулировать из одного-двух слов, но названия из большего количества лучше не дробить. Например: Sony World Photography Awards.</li>';
    text += '<li>Сначала можно записать все теги для основной темы. Например: Документальная фотография.</li>';
    text += '<li>Затем лучше определить отличительные черты для статьи. Это могут быть слова из контекста, описание фотографий и того, что на них. Например: портрет, нефть, соль, рабочие, эмигрант.</li>';
    text += '<li>Стоит перечислить любые имена собственные: имена и фамилии авторов или персонажей, названия компаний, конкурсов, стран, территорий. Например: Тоби Биндер, Азербайджан, Баку, Гейдар Алиев, Азернефть, British Petroleum.</li>';
    text += '<li>Можно использовать даты. Например, год в котором происходит действие или когда был реализован проект.</li>';
    text += '<li>Любые дополнительные данные, которые помогут в поиске нужной статьи. Даже если это просто формат или название технологии. Например: VR, дополненная реальность, галерея, видео.</li>';
    text += '</ol>';

    let modal = new tingle.modal({
      footer: false,
      stickyFooter: true,
      closeMethods: ['overlay', 'button', 'escape'],
      closeLabel: '',
      cssClass: ['modal-content']
    });

    modal.setContent(text);
    modal.open();

    event.stopPropagation();
  });

  // ======================== Кнопка Update/Publish при свернутом блоке публикации ========================
  togglePublishButton();

  jQuery('#submitdiv')
    .find('button.handlediv, h2.hndle.ui-sortable-handle')
    .on('click', togglePublishButton);

  function togglePublishButton() {
    let container = jQuery('#submitdiv');
    let clone = container.find('#publish');

    if (container.hasClass('closed')) {
      container.find('h2.hndle.ui-sortable-handle > span').after(
        clone.css({
          float: 'right',
          top: '-2px',
          position: 'relative',
          height: '28px',
          marginRight: '50px'
        })
      );
    } else {
      container.find('#publishing-action').append(clone.removeAttr('style'));
      container
        .find('h2.hndle.ui-sortable-handle')
        .find(clone)
        .remove();
    }
  }

  // ======================== Стилизация чекбоксов-картинок ========================
  jQuery(document)
    .find('.acf-hide-checkbox')
    .find('input[type="checkbox"]')
    .each(function () {
      acfToggleCheckBoxImage(jQuery(this));
    });

  jQuery(document)
    .find('.acf-hide-checkbox')
    .find('input[type="checkbox"]')
    .on('click', function () {
      acfToggleCheckBoxImage(jQuery(this));
    });

  function acfToggleCheckBoxImage(checkbox) {
    if (checkbox.attr('checked') === 'checked') {
      checkbox.siblings('img').css({
        border: '4px solid blue',
        opacity: '1'
      });
    } else {
      checkbox.siblings('img').css({
        border: '4px solid transparent',
        opacity: '0.7'
      });
    }
  }

  // ======================== Навигация по редактору контента ========================
  jQuery(document).on('click', '._was_custom_acf_nav_', function (event) {
    event.preventDefault();

    let editor = jQuery('#contenteditor');
    let adminElement = editor.find('div.layout[data-id="' + jQuery(this).data('abcn-go') + '"]');

    scrollAndBlink(
      adminElement[0],
      -(editor[0].offsetTop + jQuery('#acf-group_58b84c10297bd')[0].offsetTop)
    );
  });

  workspaceController();
});

function wasCustomCounterCheck(elem, separator, name) {
  let SEOElemMinChars;
  let SEOElemMaxChars;

  if (name === 'snippet-editor-title') {
    SEOElemMinChars = elem.val().indexOf(` ${separator} WAS`) !== -1 ? 70 : 64;
    SEOElemMaxChars = elem.val().indexOf(` ${separator} WAS`) !== -1 ? 100 : 94;
  } else if (name === 'snippet-editor-meta-description') {
    SEOElemMinChars = 160;
    SEOElemMaxChars = 300;
  } else {
    return false;
  }

  let SEOElemCurrentChars = parseInt(elem.val().length);
  let SEOElemCurrentCharsHolder = elem
    .siblings('.was-custom-seo-counter-wrapper')
    .find('.was-custom-seo-counter-current');
  let SEOElemMaxCharsHolder = elem
    .siblings('.was-custom-seo-counter-wrapper')
    .find('.was-custom-seo-counter-max');

  SEOElemCurrentCharsHolder.text(SEOElemCurrentChars);
  SEOElemMaxCharsHolder.text(SEOElemMaxChars);

  if (SEOElemCurrentChars > SEOElemMaxChars || SEOElemCurrentChars < SEOElemMinChars) {
    elem.addClass('was-custom-counter-invalid');
    SEOElemCurrentCharsHolder.addClass('was-custom-counter-invalid');
  } else {
    elem.removeClass('was-custom-counter-invalid');
    SEOElemCurrentCharsHolder.removeClass('was-custom-counter-invalid');
  }
}

function showTagsError(message = 'SOMETIMES ERROR COULD HAPPEN :(') {
  let helperModal = jQuery('.allTags-modal');

  helperModal.find('.kart-loader').hide();

  helperModal.append(
    jQuery('<div>', {
      class: 'modal-body'
    }).append(
      jQuery('<div>', {
        class: 'notice'
      }).append(
        jQuery('<p>', {
          html: message
        })
      )
    )
  );
}

function generateTagsList(tagsArray) {
  let helperModal = jQuery('.allTags-modal');

  helperModal.find('.kart-loader').hide();

  let tagsBody = jQuery('<ul>', {
    class: 'was-all-tags-list'
  });

  tagsArray.forEach(function(tag) {
    tagsBody.append(
      jQuery('<li>', {
        class: 'was-all-tags-list_item'
      }).append([
        jQuery('<a>', {
          href: '/',
          text: tag['name']
        }),
        jQuery('<sup>', {
          text: '(' + tag['count'] + ')'
        })
      ])
    );
  });

  helperModal.append(
    jQuery('<div>', {
      class: 'modal-body',
      html: tagsBody
    })
  );

  // ======================== Клик на тег в модалке (добавление в пост) ========================
  jQuery('ul.was-all-tags-list a').on('click', function (event) {
    event.preventDefault();

    jQuery('#new-tag-post_tag, #new-tag-micro_tag').val(
      jQuery(this)
        .text()
        .trim()
    );

    tagBox.userAction = 'add';
    tagBox.flushTags(jQuery('#post_tag, #micro_tag'));
  });
}
