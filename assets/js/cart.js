window.jQuery(function ($) {
    const params = window.wc_better_shipping_calculator_for_brazil_params;
    if (params == null) return;

    const selectors = params.selectors;
    const states_range = get_brazilian_states_range();
    let $state_select = null;
    let $country_select = null;
    let $city_input = null;
    let $postcode_input = null;

    // update the field elements when the cart changes
    $(document.body).on('updated_wc_div', update_field_references);
    $(document.body).on('country_to_state_changed', update_field_references);
    update_field_references();

    // detect user typing a postcode manually
    $(document.body).on('input', selectors.postcode, update_state_select);
    // detect user pasting a postcode
    $(document.body).on('focus', selectors.postcode, update_state_select);
    update_state_select();
    if (params.clear_city) clear_city_field(true);

    // maybe add postcode input mask
    if (params.add_postcode_mask) {
        $(document.body).on('input', selectors.postcode, apply_postcode_mask);
        function apply_postcode_mask(evt) {
            const input = evt.target;
            var mask = 'XXXXX-XXX';
            var content = input.value || '';
            input.value = '' !== content ? apply_mask(content, mask) : '';
            input.maxLength = mask.length;
        }
        function apply_mask(text, mask) {
            let result = '';
            // remove all non allphanumerics
            const _text = (text + '').replace(/[^a-z0-9]/gi, '');
            for (let i = 0, j = 0, len = mask.length; i < len; i++) {
                if (!_text[j]) break;
                if ('X' === mask[i]) {
                    result += _text[j];
                    j++;
                } else {
                    result += mask[i] || '';
                    j = j > 0 ? j-- : 0;
                }
            }
            return result;
        }
    }
    $(selectors.postcode).trigger('input');

    function update_state_select(evt) {
        const country = $country_select.val();
        if (!country) $country_select.val('BR').trigger('change');
        if ('BR' === $country_select.val()) {
            let postcode = get_postcode();
            $state_select.val(get_state_by_postcode(postcode));
            $state_select.trigger('change');
        }
    }

    function get_postcode() {
        let postcode = $postcode_input.val();
        // sanitize postcode
        postcode = postcode.replace(/[^0-9]/g, '').substr(0, 8);
        // complete with zeros when have 2 or more
        if (postcode.length >= 2 && postcode.length < 8) {
            postcode = padding_right(postcode, '0', 8);
        }
        return postcode;
    }

    function clear_city_field(submit = false) {
        if ('BR' !== $country_select.val()) return;
        old = $city_input.val();
        if (old) {
            $city_input.val('');
            postcode = get_postcode();
            if (submit && old && get_postcode().length === 8) {
                $(selectors.calculator).trigger('submit');
            }
        }
    }

    function update_field_references() {
        $country_select = $(selectors.country);
        $state_select = $(selectors.state);
        $city_input = $(selectors.city);
        $postcode_input = $(selectors.postcode);
        if (params.clear_city) clear_city_field(false);
    }

    function get_state_by_postcode(postcode) {
        var result = '';
        postcode = postcode.replace(/[^0-9]/g, '');

        for (var i = 0; i < states_range.length; i++) {
            if (
                is_between(postcode, states_range[i].min, states_range[i].max)
            ) {
                result = states_range[i].name;
                break;
            }
        }

        return result;
    }

    function padding_right(str, char, total) {
        char = char || '0';
        if (str.length > total) {
            total = str.length;
        }
        var max = (total - str.length) / char.length;
        for (var i = 0; i < max; i++) {
            str += char;
        }
        return str;
    }

    function is_between(x, min, max) {
        return +x >= +min && +x <= +max;
    }

    function get_brazilian_states_range() {
        return [
            {
                name: 'SP',
                min: '01000000',
                max: '19999999',
            },
            {
                name: 'RJ',
                min: '20000000',
                max: '28999999',
            },
            {
                name: 'ES',
                min: '29000000',
                max: '29999999',
            },
            {
                name: 'MG',
                min: '30000000',
                max: '39999999',
            },
            {
                name: 'BA',
                min: '40000000',
                max: '48999999',
            },
            {
                name: 'SE',
                min: '49000000',
                max: '49999999',
            },
            {
                name: 'PE',
                min: '50000000',
                max: '56999999',
            },
            {
                name: 'AL',
                min: '57000000',
                max: '57999999',
            },
            {
                name: 'PB',
                min: '58000000',
                max: '58999999',
            },
            {
                name: 'RN',
                min: '59000000',
                max: '59999999',
            },
            {
                name: 'CE',
                min: '60000000',
                max: '63999999',
            },
            {
                name: 'PI',
                min: '64000000',
                max: '64999999',
            },
            {
                name: 'MA',
                min: '65000000',
                max: '65999999',
            },
            {
                name: 'PA',
                min: '66000000',
                max: '68899999',
            },
            {
                name: 'AP',
                min: '68900000',
                max: '68999999',
            },
            {
                name: 'AM',
                min: '69000000',
                max: '69899999',
            },
            {
                name: 'RR',
                min: '69300000',
                max: '69399999',
            },
            {
                name: 'AC',
                min: '69900000',
                max: '69999999',
            },
            {
                name: 'DF',
                min: '70000000',
                max: '73699999',
            },
            {
                name: 'GO',
                min: '72800000',
                max: '76799999',
            },
            {
                name: 'TO',
                min: '77000000',
                max: '77999999',
            },
            {
                name: 'MT',
                min: '78000000',
                max: '78899999',
            },
            {
                name: 'RO',
                min: '76800000',
                max: '76999999',
            },
            {
                name: 'MS',
                min: '79000000',
                max: '79999999',
            },
            {
                name: 'PR',
                min: '80000000',
                max: '87999999',
            },
            {
                name: 'SC',
                min: '88000000',
                max: '89999999',
            },
            {
                name: 'RS',
                min: '90000000',
                max: '99999999',
            },
        ];
    }
});
