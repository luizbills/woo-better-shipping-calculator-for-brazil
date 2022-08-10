window.jQuery(($) => {
    const params = window.wc_better_shipping_calculator_for_brazil_params;
    if (!params) return;

    const refs = {};
    const body = $(document.body);
    const placeholder = params.postcode_placeholder;
    const selectors = params.selectors;

    body.on('updated_wc_div', update_refs);
    body.on('country_to_state_changed', update_refs);
    update_refs();

    body.on('keyup', selectors.postcode, apply_postcode_mask);

    function apply_postcode_mask() {
        const value = refs.postcode.val() || '';
        let postcode = value.replace(/[^0-9]/g, '').slice(0, 8);
        if (postcode.length > 5) {
            postcode = postcode.slice(0, 5) + '-' + postcode.slice(5);
        }
        refs.postcode.val(postcode);
        refs.postcode.prop('maxLength', 9); // length of "99999-999"
    }

    function update_refs() {
        refs.postcode = $(selectors.postcode);
        // update placeholder and title
        if (placeholder) {
            refs.postcode.prop('placeholder', placeholder);
            refs.postcode.prop('title', placeholder);
        }
        apply_postcode_mask();
    }
});
