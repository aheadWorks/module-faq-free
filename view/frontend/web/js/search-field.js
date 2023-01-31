define([
    'jquery',
], function ($) {
    'use strict';

    /**
     * Check whether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return value.length === 0 || value == null || /^\s+$/.test(value);
    }

    $.widget('mage.awFaqSearchField', {

        /**
         * Initialize widget
         */
        _create: function () {
            this.searchForm = $(this.options.formSelector);

            this.searchForm.on('submit', $.proxy(function (e) {
                this._onSubmit(e);
            }, this));
        },

        /**
         * Executes when the search box is submitted
         * 
         * @private
         * @param {Event} e - The submit event
         */
        _onSubmit: function (e) {
            var value = this.element.val();

            if (isEmpty(value)) {
                e.preventDefault();
            }
        },
    });

    return $.mage.awFaqSearchField;
});
