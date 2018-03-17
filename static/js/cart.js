window.jQuery(function ($) {
	var params = window.wc_better_shipping_calculator_for_brazil_params;

	if ( params == null ) return;

	var selectors = params.selectors;
	var $state_select = null;
	var $country_select = null;
	var states_range = get_brazilian_states_range();

	update_field_references();

	// para detectar que um endereço foi preenchido manualmente
	$(document.body).on('keyup', selectors.postcode, update_state_select);

	// para detectar que um endereço foi usando "colar" do context menu
	$(document.body).on('mouseenter', selectors.postcode, update_state_select);

	function update_state_select (evt) {
		if ( $country_select.val() === 'BR' ) {
			var input = evt.target.value || '';
			var postcode = input.replace(/[^0-9]/g, '');

			if (postcode.length < 8) {
				postcode = padding_right(postcode, '0', 8);
			}

			if ($state_select.length > 0) {
				$state_select.val( get_state_by_postcode(postcode) );
				$state_select.trigger('change');
			}
		}
	}

	$(document.body).on( 'updated_wc_div', update_field_references);
	$(document.body).on( 'country_to_state_changed', update_field_references);

	function update_field_references () {
		$country_select = $(selectors.country);

		if (params.hide_country_field) {
			$country_select
				.val('BR')
				.closest('#calc_shipping_country_field')
				.hide();
		} else {
			$country_select
				.closest('#calc_shipping_country_field')
				.show();
		}

		$state_select = $(selectors.state);

		if ($country_select.val() === 'BR') {
			$state_select
				.closest('#calc_shipping_state_field')
				.hide();
		} else {
			$state_select
				.closest('#calc_shipping_state_field')
				.show();
		}
	}

	function get_state_by_postcode (postcode) {
		var result = '';
		postcode = postcode.replace(/[^0-9]/g, '');

		for (var i = 0; i < states_range.length; i++) {
			if ( is_between(postcode, states_range[i].min, states_range[i].max) ) {
				result = states_range[i].name;
				break;
			}
		}

		return result;
	}

	function padding_right (str, char, total) {
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

	function is_between ( x, min, max ) {
		return (+x) >= (+min) && (+x) <= (+max);
	}

	function get_brazilian_states_range () {
		return [
			{
				name: 'SP',
				min: '01000000',
				max: '19999999'
			},
			{
				name: 'RJ',
				min: '20000000',
				max: '28999999'
			},
			{
				name: 'ES',
				min: '29000000',
				max: '29999999'
			},
			{
				name: 'MG',
				min: '30000000',
				max: '39999999'
			},
			{
				name: 'BA',
				min: '40000000',
				max: '48999999'
			},
			{
				name: 'SE',
				min: '49000000',
				max: '49999999'
			},
			{
				name: 'PE',
				min: '50000000',
				max: '56999999'
			},
			{
				name: 'AL',
				min: '57000000',
				max: '57999999'
			},
			{
				name: 'PB',
				min: '58000000',
				max: '58999999'
			},
			{
				name: 'RN',
				min: '59000000',
				max: '59999999'
			},
			{
				name: 'CE',
				min: '60000000',
				max: '63999999'
			},
			{
				name: 'PI',
				min: '64000000',
				max: '64999999'
			},
			{
				name: 'MA',
				min: '65000000',
				max: '65999999'
			},
			{
				name: 'PA',
				min: '66000000',
				max: '68899999'
			},
			{
				name: 'AP',
				min: '68900000',
				max: '68999999'
			},
			{
				name: 'AM',
				min: '69000000',
				max: '69899999'
			},
			{
				name: 'RR',
				min: '69300000',
				max: '69389999'
			},
			{
				name: 'AC',
				min: '69900000',
				max: '69920000'
			},
			{
				name: 'DF',
				min: '70000000',
				max: '73699999'
			},
			{
				name: 'GO',
				min: '72800000',
				max: '76799999'
			},
			{
				name: 'TO',
				min: '77000000',
				max: '77995000'
			},
			{
				name: 'MT',
				min: '78000000',
				max: '78899999'
			},
			{
				name: 'RO',
				min: '78900000',
				max: '78999999'
			},
			{
				name: 'MS',
				min: '79000000',
				max: '79999999'
			},
			{
				name: 'PR',
				min: '80000000',
				max: '87999999'
			},
			{
				name: 'SC',
				min: '88000000',
				max: '89999999'
			},
			{
				name: 'RS',
				min: '90000000',
				max: '99999999'
			}
		];
	}
});
