define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({

        /**
         * Generates links
         *
         * @param {Object} row
         * @returns {string}
         */
        getLabel: function (row) {
            var fieldName = '';

            if (row.name) {
                fieldName = row.name;
            } else {
                fieldName = row.title;
            }

            return '<a href="' + row.href+ '" onclick="setLocation(this.href)">' + fieldName + '</a>';
        }
    });
});
