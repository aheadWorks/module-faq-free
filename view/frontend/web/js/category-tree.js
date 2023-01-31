define([
    'jquery',
    'jquerytree'
], function ($) {
    'use strict';

    $.widget('mage.faqCategoryTree', {
        options: {
            searchSelector: '.aw-faq-sidebar-search-text-input',
            treeDataJson: []
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._initTree();
            this._bind();
            this._addFollowNodeLinkBehavior();
        },

        /**
         * Init tree
         *
         * @private
         */
        _initTree: function() {
            this.element.jstree({
                'core': {
                    'data': this.options.treeDataJson,
                    'themes': {
                        'icons': false
                    }
                },
                'search': {
                    'show_only_matches' : true
                },
                'plugins': ['wholerow', 'search'],
            });
        },

        /**
         * Event binding
         */
        _bind: function() {
            var jsTreeElement = this.element,
                searchElement = jsTreeElement.parent().find(this.options.searchSelector);

            searchElement.bind('keyup', function () {
                jsTreeElement
                    .jstree(true)
                    .search(searchElement.val());
            });

            searchElement.parents('form').bind('keypress', function (e) {
                if (e.keyCode == 13) {
                    return false;
                }
            });
        },

        /**
         * Follow the link if it defined for node
         *
         * @private
         */
        _addFollowNodeLinkBehavior: function() {
            this.element.bind("select_node.jstree", function (event, data) {
                var href = data.node.a_attr.href;
                if (href !== '#') {
                    window.location.href = href;
                } else {
                    data.instance.toggle_node(data.node);
                }
            })
        },
    });

    return $.mage.faqCategoryTree;
});