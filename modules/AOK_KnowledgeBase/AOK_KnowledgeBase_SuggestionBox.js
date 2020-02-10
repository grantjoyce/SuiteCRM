/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

/**
 * @author SalesAgility (Andrew Mclaughlan) <support@salesagility.com>
 * Date: 06/03/15
 * Using Qtip to provide KnowledgeBase article overlay.
 * Using bindWithDelay to delay request until after the user has stopped typing
 */

/**
 * Search value for suggestion box
 *
 * @returns {string}
 */
var getSearchValue = function () {
    var ret = '';

    // search in subject

    if($('#name')) {
        if (typeof $('#name').val() !== 'undefined' && $('#name').val().length) {
            ret = $('#name').val();
        } else {
            ret = $('#name').text();
        }
    }

    // also search in description

    if(
      typeof window.parent.tinyMCE !== 'undefined' &&
      typeof window.parent.tinyMCE.get('description') !== 'undefined') {
        ret += ( (ret ? ' ' : '') + window.parent.tinyMCE.get('description').getContent({format : 'text'}) );
    }

    // also search in description on detail view

    if($('#description')) {
        if (typeof $('#description').val() !== 'undefined' && $('#description').val().length) {
            ret += ( (ret ? ' ' : '') + $('#description').val() );
        } else {
            ret += ( (ret ? ' ' : '') + $('#description').text() );
        }
    }

    return ret;
};


var loadSuggestions = function() {
    var url = 'index.php?module=Cases&action=get_kb_articles';
    var search = getSearchValue();
    var dataString = 'search=' + search;
    $.ajax({
        type:"POST",
        url:url,
        data: dataString,
        success: function (data) {
            $("#suggestion_box").html(data);
        }
    });
};

var getAppliedFields = function(possibleAppliedFields) {
    var textFieldTypes = ['textarea', 'input'];
    var retAppliedFields = [];
    $.each(possibleAppliedFields, function(i, id){
        if($('#'+id).length > 0 && textFieldTypes.indexOf($('#'+id).prop('tagName').toLowerCase()) != -1) {
            retAppliedFields.push(id);
        }
    });
    return retAppliedFields;
};

$(document).ready(function() {
  
    // searching only when the search value changed

    var searchValue = '';
    setInterval(function(){
        var newSearchValue = getSearchValue();
        if(searchValue != newSearchValue) {
            searchValue = newSearchValue;
            loadSuggestions();
        }
    }, 1000);

    var intervalForBindWithDelay = setInterval(function(){
        if(typeof $('#name').bindWithDelay === 'function') {

            //Get KnowledgeBase article suggestions based on what is typed into the name field
            $('#name').bindWithDelay("keyup", function () {
                loadSuggestions();
            }, 1000);

            clearInterval(intervalForBindWithDelay);
        }
    }, 300);

    loadSuggestions();

    var appliedFields = getAppliedFields(['resolution', 'update_text']);

    //open article in tool-tip on hover over suggestion box list items
    $("body").on("mouseover",'.kb_article', function () {
        var title = SUGAR.language.get('Cases', 'LBL_TOOL_TIP_BOX_TITLE');
        var url = 'index.php?module=Cases&action=get_kb_article';
        // Updates existing tool-tips content via ajax
        var article = $(this).data( "id" );
        var dataStuff = {
            article: article,
            appliedBtns: appliedFields
        };
        if($("#suggestion_box").data('qtip') ) {
            $.ajax({
                type:"POST",
                url:url,
                data: dataStuff,
                success: function (data) {
                    $(".qtip-content").html(data);
                }
            });
        }
        else {//Creates new qtip tool-tip
            $("#suggestion_box").qtip({
                content: {
                    text: "Loading...",
                    ajax:{
                        url: url, // Use href attribute as URL
                        type: 'POST', // POST or GET
                        data: dataStuff, // Data to pass along with your request
                        success: function(data, status) {
                            // Set the content manually (required!)
                            this.set('content.text', data);
                        }
                    },
                    title: {
                        button: true, //show close button
                        text: title
                    }
                },
                position: { //position of tool-tip relative to suggestion box
                    my: 'middle right',
                    at: 'middle left',
                    adjust: {
                        mouse: false,
                        scroll: true,
                        y: 0,
                        x: 0
                    }
                },
                show: {
                    event: 'mouseover', // Show it on click...
                    ready: true, // Show the tooltip when ready
                    delay: 1000,
                    effect: function() {
                        $(this).fadeTo(800, 1);
                    }
                },
                hide: false,
                style: {
                    classes : 'qtip-default qtip qtip qtip-tipped qtip-shadow', //qtip-rounded'
                    tip: {
                        offset: 0
                    }
                }
            });
        }
    });

    var isTinyMCEField = function(id) {
        return typeof window.parent.tinyMCE !== 'undefined' && !!window.parent.tinyMCE.get(id);
    };

    var getTextFieldValue = function(id) {
        var text = null;
        if(isTinyMCEField(id)) {
            text = window.parent.tinyMCE.get(id).getContent({format : 'text'});
        } else {
            text = $('#'+id).val();
        }
        return text;
    };

    var setTextFieldValue = function(id, text) {
        if(isTinyMCEField(id)) {
            window.parent.tinyMCE.get(id).setContent(text, {format : 'text'});
        } else {
            $('#'+id).val(text);
        }
    };

    $.each(appliedFields, function(i, id){
        $('body').on('click', '#use_'+id, function(){
            if($('#additional_info_p').text() !=  getTextFieldValue(id)){
                var sel = '[field="'+id+'"]';
                if(!$(sel).length) {
                    sel = '#'+id;
                }
                if(!$(sel).length) {
                    throw 'input element not found: '+id;
                } else {
                    $('#additional_info_p').effect("transfer", {
                        to: sel,
                        className: "transfer"
                    }, 1000, function () {
                        setTimeout(function () {
                            var text = $('#additional_info_p').text();
                            setTextFieldValue(id, text);
                        }, 300);
                    });
                }
            }
        });
    });

});


$(function(){
    setInterval(function(){
        // qtip forced into screen area
        $('.qtip').each(function(i,e){
            if(parseInt($(e).css('left'))<0) {
                $(e).animate({left: 0});
            }
        });

    }, 300);
});