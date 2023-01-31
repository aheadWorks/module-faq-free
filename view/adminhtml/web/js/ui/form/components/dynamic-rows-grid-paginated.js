define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid'
], function (dynamicRowsGrid) {
    'use strict';

    return dynamicRowsGrid.extend({
        defaults: {
            firstPage: 0
        },

        /**
         * Reload data
         */
        reload: function () {
            this._super();
            this.parsePagesData(this.recordData()); //Magento Bug when changing page size not changed pages count
            this.changePage(this.firstPage);
        },

        /**
         * Check spinner
         */
        checkSpinner: function (elems) {
            this.showSpinner(!(!this.recordData().length
                || elems && elems.length === parseInt(this.pageSize)
                || elems && this.recordData().length === elems.length
                || elems && ((this.currentPage() - 1) * this.pageSize + elems.length) === this.recordData().length
            ));
        },
    });
});
