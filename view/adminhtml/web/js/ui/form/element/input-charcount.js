define([
    'ko',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function (ko, Abstract, $t) {
    'use strict';

    return Abstract.extend({
        defaults: {
            warningLevel: 60,
            hintText: $t('{count} characters used. Recommended max length is 50-{length} characters'),
            template: 'Aheadworks_FaqFree/ui/form/element/input-charcount'
        },

        /**
         * Initialize observable properties
         */
        initObservable: function () {
            var hintText;

            this._super();
            this.charCount = ko.computed(function () {
                var value = this.value();

                return value ? value.length : 0;
            }, this);
            this.hint = ko.computed(function () {
                hintText = this.hintText.replace('{count}', this.charCount());
                hintText = hintText.replace('{length}', this.warningLevel.toString());

                return hintText;
            }, this);
            this.warning = ko.computed(function () {
                return this.charCount() > this.warningLevel;
            }, this);

            return this;
        },

        /**
         * On key up event handler.
         * @param {Object} data
         * @param {Event} event
         */
        onKeyUp: function (data, event) {
            this.value(event.target.value);
        }
    });
});
