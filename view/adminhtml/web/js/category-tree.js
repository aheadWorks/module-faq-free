define([
    'jquery',
    'underscore',
    'mage/storage',
    'Magento_Ui/js/lib/spinner',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'jquery-ui-modules/widget',
    'jquerytree'
], function ($, _, storage, loader, alert, $t) {
    'use strict';

    $.widget('mage.faqCategoryTree', {
        options: {
            initSelector: '.tree-init',
            expandButtonSelector: '.expand-tree-button',
            collapseButtonSelector: '.collapse-tree-button',
            formLoaderId: 'faq_category_form.faq_category_form',
            moveUrl : '',
            categories: [],
            checkCallback: true,
            treeConfig: {
                plugins: ['dnd', 'conditionalselect'],
                core: {
                    data: [],
                    check_callback : true
                },
                dnd: {
                    drag_selection: false,
                    copy: false
                }
            }
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._prepareTreeConfig();
            this._bind();
            this.element.find(this.options.initSelector).jstree3(this.options.treeConfig);
        },

        /**
         * Prepare tree config
         *
         * @private
         */
        _prepareTreeConfig: function () {
            this.options.treeConfig.core.data = this.options.categories;
            this.options.treeConfig.core.check_callback = this.options.checkCallback;
            this.options.treeConfig.conditionalselect = $.proxy(this._redirectToEdit, this);
        },

        /**
         * Bind callbacks
         *
         * @private
         */
        _bind: function () {
            var tree = this.element.find(this.options.initSelector),
                collapseButton = this.element.find(this.options.collapseButtonSelector),
                expandButton = this.element.find(this.options.expandButtonSelector);

            if (tree.length) {
                tree.on('move_node.jstree', $.proxy(this._moveNode, this));
                if (collapseButton.length) {
                    collapseButton.on('click', function () {
                        tree.jstree3('close_all');
                    });
                }
                if (expandButton.length) {
                    expandButton.on('click', function () {
                        tree.jstree3('open_all');
                    });
                }
            }
        },

        /**
         * Move node
         *
         * @param {Event} event
         * @param {Object} data
         * @private
         */
        _moveNode: function (event, data) {
            var tree = this.element.find(this.options.initSelector),
                node = data.node,
                self = this;

            if (node) {
                this.showLoader();
                storage.post(
                    this.options.moveUrl,
                    {
                        form_key: this.getFormKey(),
                        nodes_data: this._getNodesPositionData(node)
                    },
                    true,
                    'application/x-www-form-urlencoded; charset=UTF-8'
                ).done(
                    function (response) {
                        if (!response.success) {
                            self.showErrorAlert(response.message);
                        }
                    }
                ).fail(
                    function () {
                        self.showErrorAlert($t('Something went wrong while moving the category.'));
                    }
                ).always(
                    function () {
                        self.hideLoader();
                    }
                );
            }
        },

        /**
         * Retrieve nodes position data
         *
         * @param {Object} currentNode
         * @return {Array}
         * @private
         */
        _getNodesPositionData: function (currentNode) {
            var tree = this.element.find(this.options.initSelector),
                prevNode = tree.jstree3('get_node', tree.jstree3('get_prev_dom', currentNode, true)),
                parentNode = currentNode.parent !== '#' ? tree.jstree3('get_node', currentNode.parent) : false,
                sortOrderStart = parentNode ? parseInt(parentNode.data.sort_order) : 0,
                node = currentNode,
                data = [],
                firstChildNode;

            if (!prevNode) {
                sortOrderStart = sortOrderStart + 1000;
                prevNode = {data: {sort_order: sortOrderStart}};
            }

            while (node && node.id) {
                node.data.sort_order = parseInt(prevNode.data.sort_order) + 10;
                data.push({
                    target_id: node.id,
                    parent_id: node.parent,
                    path: this._getNodePath(node),
                    sort_order: node.data.sort_order
                });
                if (!_.isEmpty(node.children)) {
                    firstChildNode = tree.jstree3('get_node', _.first(node.children));
                    data = _.union(data, this._getNodesPositionData(firstChildNode));
                }
                prevNode = node;
                node = tree.jstree3('get_node', tree.jstree3('get_next_dom', node, true));
            }

            return data;
        },

        /**
         * Retrieve node path
         *
         * @param {Object} node
         * @return string
         * @private
         */
        _getNodePath: function (node) {
            var path = node.id;

            _.each(node.parents, function (parentId) {
                if (parentId !== '#') {
                    path = parentId + '/' + path;
                }
            });

            return path;
        },

        /**
         * Redirect to edit
         *
         * @param {Object} node
         * @return {Boolean}
         * @private
         */
        _redirectToEdit: function (node) {
            window.open(node.a_attr.href, '_self');

            return false
        },

        /**
         * Retrieve form key
         *
         * @returns {String}
         */
        getFormKey: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return window.FORM_KEY;
        },

        /**
         * Hides loader.
         */
        hideLoader: function () {
            loader.get(this.options.formLoaderId).hide();
        },

        /**
         * Shows loader.
         */
        showLoader: function () {
            loader.get(this.options.formLoaderId).show();
        },

        /**
         * Show error alert
         *
         * @param {String} content
         */
        showErrorAlert: function(content) {
            alert({
                title: $t('Error'),
                content: content,
                actions: {
                    always: function () {
                        window.location.reload();
                    }
                }
            });
        }
    });

    return $.mage.faqCategoryTree;
});
