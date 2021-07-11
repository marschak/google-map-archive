<?php get_header(); 

global $wpdb;
$min_price = $wpdb->get_var( "SELECT MIN(cast(meta_value as unsigned)) FROM $wpdb->postmeta WHERE meta_key = 'price' AND meta_value IS NOT NULL AND meta_value <> 0" );
$max_price = $wpdb->get_var( "SELECT MAX(cast(meta_value as unsigned)) FROM $wpdb->postmeta WHERE meta_key = 'price' AND meta_value IS NOT NULL AND meta_value <> 0" );
$rooms = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key='rooms'  AND `meta_value` != '' ORDER BY  meta_value ASC ", OBJECT);

?>

<section class="filter">

	<div class="filter-bg"></div>
	<!-- /.filter-bg -->
	<form class="filter-wrap" action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter">

		<div class="filter-search">
			<div class="filter-input">
				<?php
		if( $terms = get_terms( array( 'taxonomy' => 'listing_type', 'orderby' => 'name' ) ) ) : 
			
			echo '<select class="filter-input__select" name="type" id="type"  value="'.$_POST["type"] .'"><option value="" >Type</option>';
			foreach ( $terms as $term ) :
				if ($term->term_id == $_POST["type"]) { $selected = 'selected';}
				echo '<option '.$selected.' value="' . $term->term_id . '" >' . $term->name . '</option>'; // ID of the category as the value of an option
			endforeach;
			echo '</select>';
		endif;
	?>
			</div>
			<!-- /.filter-input -->
			<div class="filter-input" name="Pieces" id="Pieces">
				<select class="filter-input__select" name="Pieces" id="Pieces" value="<?php $_POST['Pieces'] ?>">
					<option selected >Pieces</option>
					<? if ( $rooms) 
        {
		foreach ( $rooms as $room) : 
			if ($room->meta_value == $_POST["Pieces"]) { $selected = 'selected';}
      echo '<option '.$selected.' value='.$room->meta_value.'>'.$room->meta_value.'</option>';
        endforeach; 
		}
		?>
				</select>

			</div>
			<!-- /.filter-input -->
			<div class="filter-input filter-input__ref">
				<input class="filter-input__input" type="text" placeholder="Reference" name="Ref" id="Ref">
			</div>
			<!-- /.filter-input -->
		</div>
		<!-- /.filter-search -->
		<div class="filter-price">
			<div class="filter-input filter-price-input">
				<input id="price_min" name="price_min" class="filter-input__input filter-price-input__input" type="text"
					placeholder="€ min.">
			</div>
			<!-- /.filter-input -->
			<div class="filter-slider">
				<div class="filter-slider__label">
					Price range
				</div>
				<div id="slider" class="filter-slider__price"></div>
			</div> <!-- /.filter-slider -->
			
			
				<input id="price_max" name="price_max" class="filter-input__input filter-price-input__input" value="<?php echo $_POST['price'] ?>" type="text"
					placeholder="€ max" >
			</div>
			
			<!-- /.filter-input -->
			<input id="area" name="area" type="hidden" value="<?php $_POST['area'] ?>">
			<!-- button  style="background:red; "class="btn ">submit</!-->
		</div> <!-- /.filter-price -->

		<!--input type="hidden" name="action" value="artfilter"-->
		<input class="filter-sort" id="filterSort" type="hidden" name="sort" value="update">
	</form>

	<!-- /.filter-wrap -->
</section>
<section class="sort">
	<div class="sort-wrap">

		<div class="sort-select">

			<div class="sort-select__caption">
				Sort by :
			</div>
			<!-- /.sort-select__caption -->

			<select class="sort-select__select">
				<option selected value="update">Newest</option>
				<option value="price">Price</option>
			</select>

			<div class="sort-select__buttons">

				<button class="sort-select__view">
					List
				</button>
				<!-- /.sort-select__view -->
				<button class="sort-select__view active">
					Map
				</button>
				<!-- /.sort-select__view -->

			</div>
			<!-- /.sort-select__buttons -->



		</div>
		<!-- /.sort-select -->

	</div>
	<!-- /.sort-wrap -->
</section>

<section class="map">

	<div class="map-wrap left">
		<style type="text/css">
			.acf-map {
				width: 100%;
				height: 400px;
				border: #ccc solid 1px;
				margin: 20px 0;
			}
			.acf-map img {
				max-width: inherit !important;
			}
		</style>
		<map class="map-col map-col__map left ">

			<div class="acf-map" data-zoom="11">
				<?php 
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$location = get_field( 'map' );	?>
				<div class="marker" data-lat="<?php echo $location[ lat ]; ?>"
					data-lng="<?php echo $location[ lng ]; ?>" data-icon="">
					<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
					<div class="location-image"><?php the_post_thumbnail('medium'); ?></div>
					<p><?php echo $location['address']; ?></p>
				</div>
				<?
	endwhile;
	endif;?>

			</div>

			<div class="map-buttons">
				<button class="map-button">
					Open map in full size
				</button>
				<!-- /.map-button -->
				<button class="map-button map-button__top">
					Map on top
				</button>
				<!-- /.map-button -->
				<button class="map-button map-button__left none">
					Map on left
				</button>
				<!-- /.map-button -->
			</div> <!-- /.map-buttons -->

		</map>
		<div class="map-col map-col__views left" id="response">
			<?php if ( have_posts() ) : ?>
			<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'content', 'listing' );
				}
			?>
		</div>
		<?php //porto_pagination(); ?>
	</div>
	<?php wp_reset_postdata(); ?>
	<?php else : ?>
	<p><?php esc_html_e( 'Aucun élément de ne correspond à votre recherche.', 'colibrity' ); ?></p>
	<?php endif; ?>
</section>

<script>

		jQuery(function ($) {
			$('#filter').change(function () {
				let filter = $('#filter');
				$.ajax({
					url: filter.attr('action'),
					data: filter.serialize() + '&action=artfilter', // form data
					type: filter.attr('method'), // POST
					beforeSend: function (xhr) {
						filter.find('button').text('Processing...'); // changing the button label
					},
					success: function (data) {
						filter.find('button').text(
						'Apply filter'); // changing the button label back
						$('#response').html(data); // insert data
						$.getScript('<?php echo get_template_directory_uri(); ?>/js/script.js'); 
					}
				});
				$.ajax({
				url: filter.attr('action'),
				data: filter.serialize()+ '&action=mapfilter',
				type: filter.attr('method'), // POST
				beforeSend: function (xhr) {
					filter.find('button').text('Processing...'); // changing the button label
				},
				success: function (data) {
					filter.find('button').text(	'Apply filter'); // changing the button label back
					$('.map-col__map').html(data); // insert data
					$.getScript('<?php echo get_template_directory_uri(); ?>/js/dealer.js'); 
				
				}
			});
				return false;
		});
	});
	
	
	// toggle list-map


	const selectButtons = document.querySelectorAll('.sort-select__buttons button');
	const sectionMap = document.querySelector('.map');
	const mapCol = sectionMap.querySelectorAll('.map-col');
	const btnOnMap = document.querySelectorAll('.map-buttons .map-button');
	const mapWrap = document.querySelector('.map-wrap');


	selectButtons.forEach((item, i) => {


		item.addEventListener('click', () => {

			selectButtons.forEach((item, i) => {
				item.classList.remove('active');
			});
			item.classList.add('active');

			if (i == 0) {
				mapCol.forEach(item => {
					item.classList.add('list');
					mapWrap.classList.remove('left');
					mapWrap.classList.remove('map');
					mapWrap.classList.remove('top');
					item.classList.remove('left');
					item.classList.remove('map');
					item.classList.remove('top');
					item.classList.remove('full');
				});
			} else if (i == 1) {
				btnOnMap[1].classList.remove('none');
				btnOnMap[2].classList.add('none');
				mapCol.forEach(item => {
					item.classList.remove('list');
					item.classList.add('left');
					mapWrap.classList.add('left');

					mapCol.forEach(item => {
						mapWrap.classList.remove('map');
						mapWrap.classList.remove('top');
						item.classList.remove('map');
						item.classList.remove('top');
						item.classList.remove('full');
					});

				});
			};

		})

	});

	btnOnMap.forEach((item, i) => {

		item.addEventListener('click', () => {

			if (i == 1) {

				mapWrap.classList.add('map');
				mapWrap.classList.add('top');
				mapWrap.classList.remove('left');
				mapCol.forEach((item, i) => {
					item.classList.add('map');
					item.classList.add('top');
					item.classList.remove('left');
					item.classList.remove('full');

					btnOnMap[0].classList.remove('none');
					btnOnMap[1].classList.add('none');
					btnOnMap[2].classList.remove('none');

				});

			}

			if (i == 2) {


				mapCol.forEach(item => {
					item.classList.add('left');
					mapWrap.classList.add('left');
					mapWrap.classList.remove('map');
					mapWrap.classList.remove('top');
					item.classList.remove('map');
					item.classList.remove('top');
					item.classList.remove('full');
				});
				btnOnMap[0].classList.remove('none');
				btnOnMap[1].classList.remove('none');
				btnOnMap[2].classList.add('none');

			}

			if (i == 0) {
				mapCol.forEach(item => {
					item.classList.add('full');
					item.classList.remove('left');
					mapWrap.classList.remove('left');
					mapWrap.classList.remove('map');
					mapWrap.classList.remove('top');
					item.classList.remove('map');
					item.classList.remove('top');
				});
				btnOnMap[0].classList.add('none');
				btnOnMap[1].classList.remove('none');
				btnOnMap[2].classList.remove('none');
			}


		});

	});


	// toggle list-map
</script>

<script>
	const priceSlider = document.querySelector('.filter-slider__price');
	let valuesSlider;
	const filterPriceInput = document.querySelectorAll('.filter-price-input__input');
	const filterRefInput = document.querySelector('.filter-input__ref input');

	const setStyle = (item) => {
		if (item.value == '') {
			item.parentNode.classList.remove('active');
		} else {
			item.parentNode.classList.add('active');
		}
	};

	noUiSlider.create(priceSlider, {


		<?php
		if (isset($_POST['price']) && $_POST['price']) { echo "start:[".$min_price.",".$_POST['price']."],";} 
				else { echo 'start: [ '.$min_price.' , '.$max_price.' ],';}		
		?>
		//start : [ <? //echo $min_price ?> , <? //echo $max_price ?> ],
		tooltips : true,
		connect: true,
		padding: 0,
		//	tooltips: [ wNumb({decimals: 0}),  wNumb({decimals: 0})],
		step: 500,
		range: {
			'min': <? echo $min_price ?> ,
			'max': <? echo $max_price ?>
		},
		format: {
			to: function (value) {
				return parseInt(value);
			},
			from: function (value) {
				return parseInt(value);
			}
		},

	});

	filterPriceInput.forEach((item, i) => {
		item.value = priceSlider.noUiSlider.get()[i];
		setStyle(item);
	});

	priceSlider.noUiSlider.on('change', (values, handler) => {

		valuesSlider = values;
		//	console.log(valuesSlider);

		//	console.log(priceSlider.noUiSlider.get());
		// console.log(priceSlider.noUiSlider.set([3, 70]));

		filterPriceInput.forEach((item, i) => {
			item.value = priceSlider.noUiSlider.get()[i];
			setStyle(item);
		});
		
		filterChange();

	});

	filterPriceInput.forEach((item, i) => {
		item.addEventListener('keyup', () => {
			item.value = item.value.replace(/[^\d\.]/g, '');
			setStyle(item);
		});
		item.addEventListener('change', () => {
			priceSlider.noUiSlider.set([filterPriceInput[0].value, filterPriceInput[1].value]);
			setStyle(item);
		});
		// item.value = priceSlider.noUiSlider.get()[i];
	});

	// 	filterRefInput.addEventListener('keyup', (e) => {
	// 		e.target.value = e.target.value.replace(/\D/g, '');
	// 	});
	
	const filterSort = document.querySelector('.filter-sort');
	
	const sortSelect = document.querySelector('.sort-select__select');
	console.log(sortSelect);

	sortSelect.addEventListener('change', () => {
		filterSort.value = sortSelect.value;
		console.log(sortSelect.value);
		console.log(filterSort.value);
	});
	

	// filter-slider
</script>
<?php
get_footer();