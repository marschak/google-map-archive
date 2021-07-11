<?php get_header(); 

global $wpdb;

$cat = get_term_by( 'slug', $_GET['cat'], 'listing_cat');
$cat_id = $cat->term_id;
//echo 	'cat'. $cat_id;
$arrondissement = get_term_by( 'slug', $_GET['arrondissement'], 'listing_cat');
$arrondissement_id = $arrondissement->term_id;
if($_POST['price']) {
    $min_price = 0;
    $max_price = $_POST['price'];
} else {
    if($cat_id) {
        $argsPrice = array(
            'posts_per_page'=> -1,
            'post_type'     => 'listing',
            'meta_key'      => 'price',
         // 'meta_value'    => $development_id,
            'tax_query' => array(
                array(
                    'taxonomy' => 'listing_cat', 
                    'field'    => 'id',
                    'terms'    => $cat_id,
                ),
            ),
        );
        $properties_query = new WP_Query( $argsPrice ); 
        $pricesP = array();

        if( $properties_query->have_posts() ):
            while( $properties_query->have_posts() ) : $properties_query->the_post();
                $price = get_field('price'); 
                if(isset($price) && !empty($price)){
                    $pricesP[] = $price; 
                }
            endwhile;
            $max_price = max($pricesP);
            $min_price = min($pricesP);

        endif; wp_reset_query(); 
    } else {
        $min_price = $wpdb->get_var( "SELECT MIN(cast(meta_value as unsigned)) FROM $wpdb->postmeta WHERE meta_key = 'price' AND meta_value IS NOT NULL AND meta_value <> 0" );
        $max_price = $wpdb->get_var( "SELECT MAX(cast(meta_value as unsigned)) FROM $wpdb->postmeta WHERE meta_key = 'price' AND meta_value IS NOT NULL AND meta_value <> 0" );
    }
}
$rooms = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key='rooms'  AND `meta_value` != '' ORDER BY  meta_value ASC ", OBJECT);
$areas = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key='size'  AND `meta_value` != '' ORDER BY  meta_value ASC ", OBJECT);
?>

 
<section class="filter">
<?php 
   
    $district = $_POST['District'];
?>
	<div class="filter-bg"></div>
	<!-- /.filter-bg -->
	<form class="filter-wrap" action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter">
		<div class="filter-search">
			<div class="main-input main-input-test filter-input__wht" id="typesList">
								
                <input class="main-input__select" type="text" placeholder="Type" readonly>
                <input class="main-input__select-hidden" type="hidden" name="type" id="type" placeholder="Type">
                <input class="input-hidden-start-value" name="type-hidden" type="hidden" value="<?php echo $_POST['Type']; ?>">
                <div class="main-input__listing">
                    <?php
                        if( $terms = get_terms( array( 'taxonomy' => 'listing_type', 'orderby' => 'name' ) ) ) :
                            foreach ( $terms as $term ) : 
                                echo '<div class="main-input__listing-list">
                                    <label>
                                        <input type="checkbox" class="main-input__listing-list__checkbox" name="type2[]" id="type2" value="'.$term->term_id.'">
                                        <span class="main-input__listing-list__check"></span>
                                        <span data-area-value="'.$term->term_id.'" class="main-input__listing-list__caption" value="'.$term->term_id.'">'.$term->name.'</span>
                                    </label>
                                </div>';
                            endforeach; 
                        endif;
                    ?>
                    <div class="main-input__listing-clear">
                        <span class="main-input__listing-clear__caption">Tout effacer</span>
                    </div>
                </div>
                
            </div>
            <!-- /.main-input -->
			<div class="main-input main-input-test filter-input__wht">
								
                <input class="main-input__select" type="text" placeholder="Pieces" readonly>
                <input class="main-input__select-hidden" type="hidden" name="Pieces" id="Pieces" placeholder="Surface">
                <input class="input-hidden-start-value" name="Pieces-hidden" type="hidden" value="<?php echo $_POST['Pieces']; ?>">
                <div class="main-input__listing">
                    <?php
                        if ( $rooms) :
                            foreach ( $rooms as $room) : 
                                echo '<div class="main-input__listing-list">
                                    <label>
                                        <input type="checkbox" class="main-input__listing-list__checkbox">
                                        <span class="main-input__listing-list__check"></span>
                                        <span data-area-value="'.$room->meta_value.'" class="main-input__listing-list__caption">'.$room->meta_value.'</span>
                                    </label>
                                </div>';
                            endforeach; 
                        endif;
                    ?>
                    <div class="main-input__listing-clear">
                        <span class="main-input__listing-clear__caption">Tout effacer</span>
                    </div>
                </div>
                
            </div>
             <!-- /.main-input -->            
			<div class="main-input main-input-test filter-input__wht">
                <input class="main-input__select" type="text" placeholder="Max Surface" value="<?php echo $_POST['area']; ?>" id="newAreaText" readonly>
                <input class="main-input__select-hidden" type="hidden" name="area" id="newArea" placeholder="Max Surface" value="<?php echo $_POST['area']; ?>">
                <div class="main-input__listing main-input_radio">
                    <?php
                        $elements = array();
                        foreach ($areas as $element) :
                            $elements[] = $element->meta_value;
                        endforeach;
                        $areaValues = ceil(max($elements) / 50) + 1;
                        if ( $elements) :
                            for ( $i = 1; $i < $areaValues; $i++) : 
                                $isChecked = '';
                                if( $i * 50 == $_POST['area'] ) {
                                    $isChecked = 'checked="checked"';
                                }
                                echo '<div class="radio">
                                    <label>
                                        <input type="radio" name="areaRadios" id="" value="'.($i * 50).'" '. $isChecked .'>
                                        <span class="checkmark"></span>
                                        <span>< '.($i * 50).' m²</span>
                                    </label>
                                </div>';
                            endfor; 
                        endif;
                    ?>
                    <div class="main-input__listing-clear" id="readioClear">
                        <span class="main-input__listing-clear__caption">Tout effacer</span>
                    </div>
                </div>
                
            </div>
            <!-- /.main-input -->
			<div class="filter-input filter-input__ref">
				<input class="filter-input__input" type="text" placeholder="Reference" name="Ref" id="Ref">
			</div>
			<!-- /.filter-input -->
		</div>

		<!-- /.filter-search -->
		<div class="filter-price">
			<div class="filter-input filter-price-input">
				<input id="price_min" name="price_min" class="filter-input__input filter-price-input__input" type="text" placeholder="€ min.">
			</div>
			<!-- /.filter-input -->
			<div class="filter-slider">
				<div class="filter-slider__label">Gamme de prix</div>
				<div id="slider" class="filter-slider__price"></div>
			</div>
            <!-- /.filter-slider -->
			<div class="filter-input filter-price-input">
				<input id="price_max" name="price_max" class="filter-input__input filter-price-input__input" value="<?php echo $_POST['price'] ?>" type="text" placeholder="€ max" >
			</div>
			<!-- /.filter-input -->
			<!-- button  style="background:red; "class="btn ">submit</!-->
		</div>
        <!-- /.filter-price -->

		<!--input type="hidden" name="action" value="artfilter"-->
		<input class="filter-sort" id="filterSort" type="hidden" name="sort" value="date">

        <?php	
            // $cat = get_term_by( 'slug', $_GET['cat'], 'listing_cat');
            // $cat_id = $cat->term_id;
            // //echo 	'cat'. $cat_id;
            // $arrondissement= get_term_by( 'slug', $_GET['arrondissement'], 'listing_cat');
            // $arrondissement_id = $arrondissement->term_id;
        ?>
		
		<input id="tag" name="tag" type="hidden" value="<?php echo $cat_id ;?>,<?php echo $arrondissement_id ; ?>">
        <input id="district" name="District" type="hidden" value="<?php echo $district; ?>">
		</form> 
	<!-- /.filter-wrap -->
</section>
<section class="sort">
	<div class="sort-wrap">
        <div class="sort-btn">
            <style>
                .btn.btn-clear {
                    position: relative;
                    padding-right: 30px;
                }
                .btn.btn-clear::after{
					content: '';
					position: absolute;
					width: 14px;
					height: 2px;
					right: 7px;
					top: 50%;
					transform: translate(0, -50%) rotate(-45deg);
                    background: #b5b5b5;
                }
				.btn.btn-clear::before{
					content: '';
					position: absolute;
					width: 14px;
					height: 2px;
					right: 7px;
					top: 50%;
					transform: translate(0, -50%) rotate(45deg);
                    background: #b5b5b5;
                }
            </style>
            <a href=""  Class="btn btn-clear" id="clear">Tout effacer</a>
            <?php if($district) { ?>
                <a href=""  Class="btn btn-clear" id="districtClear"><?php echo $district; ?></a>
            <?php } ?>
        </div>
		<div class="sort-select">
			<div class="sort-select__caption">Trier par :</div>
			<!-- /.sort-select__caption -->
            <!-- <select class="sort-select__select">
				<option selected value="date">Nouveau 1</option>
				<option value="meta_value_num">Prix</option>
			</select> -->
			
			<div class="sort-select__select">
				<span class="sort-select__select-value">Nouveau</span>
				<div class="sort-select__select-list">
					<span data-value="date">Nouveau</span>
					<span data-value="meta_value_num">Prix</span>
				</div>
			</div>
			
			<div class="sort-select__buttons">
				<button class="sort-select__view">Liste</button>
				<!-- /.sort-select__view -->
				<button class="sort-select__view active">Carte</button>
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
				height: 100%;
				border: #ccc solid 1px;
				margin: 20px 0;
			}
			.acf-map img {
				max-width: inherit !important;
			}
		</style>
		<div class="map-col map-col__map left ">
			<div class="acf-map" data-zoom="11">
				<?php 
                    if ( have_posts() ) : while ( have_posts() ) : the_post();
                        $location = get_field( 'map' );	
                ?>
                    <div class="marker" data-lat="<?php echo $location[ 'lat' ]; ?>" data-lng="<?php echo $location[ 'lng']; ?>" data-icon="">
                        <?php get_template_part( 'content', 'maplisting' ); ?>
                    </div>
                <?php
                    endwhile;
                    endif;
                ?>
            </div>
			<div class="map-buttons">
				<button class="map-button">Ouvrir la carte en taille réel</button>
				<!-- /.map-button -->
				<button class="map-button map-button__top">Carte en haut</button>
				<!-- /.map-button -->
				<button class="map-button map-button__left none">Carte à gauche</button>
				<!-- /.map-button -->
			</div> <!-- /.map-buttons -->
        </div>
		<div class="map-col map-col__views left" id="response">
            <?php
            if ( have_posts() ) :
				while ( have_posts() ) {
					the_post();
					get_template_part( 'content', 'listing' );
				}
			    //pagination();
                wp_reset_postdata();
            else : 
            ?>
                <p><?php esc_html_e( 'Aucun élément de ne correspond à votre recherche.', 'colibrity' ); ?></p>
            <?php endif; ?>
		</div>
    </div>

</section>

<script>
	const filterChange = () => {
		jQuery(function ($) {
            let filter = $('#filter');
            // console.log('ZZ-'+filter.attr('method'));
            // console.log('ZZ-'+filter.attr('action'));
            $.ajax({
                url: filter.attr('action'),
                data: filter.serialize() + '&action=artfilter', // form data
                type: filter.attr('method'), // POST
                beforeSend: function (xhr) {
                    filter.find('button').text('Processing...'); // changing the button label
                },
                success: function (data) {
                    filter.find('button').text('Apply filter'); // changing the button label back
                    $('#response').html(data); // insert data
                    let screenWidth = window.innerWidth;
                    let mapCards = document.querySelectorAll('.map-card');
                    // 	console.log(mapCards);

                    mapCards.forEach((item) => {
    
                        let buttonsNav = item.querySelector('.map-card__link-buttons');
                        let iconsSociety = item.querySelector('.map-card__icons');
                        let cardsNav = item.querySelectorAll('.map-card__link-buttons_arrow');
                        let images = item.querySelectorAll('.map-card__link_img');
                        let imageActive;
                        if (images.length > 0) {
                            images[0].classList.add('active');
                        }
                        images.forEach((item, i) => {
                            if (item.classList.contains('active')) {
                                imageActive = i;
                            }
                        })
                        if (screenWidth > 1052) {
                            item.addEventListener('mouseover', (e) => {                                
								if (images.length > 1) {
									buttonsNav.style.display = 'flex';
								}
								iconsSociety.style.display = 'flex';
                            });
                            item.addEventListener('mouseout', (e) => {
                                buttonsNav.style.display = 'none';
                                iconsSociety.style.display = 'none';
                            });
                        }
    
                        cardsNav.forEach((item, i) => {
                            item.addEventListener
                            item.addEventListener('click', (e) => {

                                if (i === 0 & imageActive != 0) {
                                    imageActive = imageActive - 1;
                                } else if (i === 1 & imageActive < images.length-1) {
                                    imageActive = imageActive + 1;				
                                } else if (i === 1 & imageActive == images.length-1) {
                                    imageActive = 0;
                                } else {
                                    imageActive = images.length-1;
                                }

                                images.forEach((item, i) => {
                                    item.classList.remove('active');
                                })
                                images[imageActive].classList.add('active');

                            });
                        });

                    });
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
                    $('.acf-map').html(data); // insert data
                    $.getScript('<?php echo get_template_directory_uri(); ?>/js/dealer.js');
                }
            });
            return false;
		});
    };

	jQuery('#clear').click(function(){
        $('#filter')[0].reset();
        $('#filter').submit();
        return false;
    });
 
    
	filterChange();
	jQuery(function ($) {
        // Get value from footer menu
        var getPieces = "<?php echo $_POST["Type"]; ?>";
        if( getPieces == '' ){
            // console.log('has value');
            $('#typesList .main-input__listing-list').each(function(){
                $(this).find('input.main-input__listing-list__checkbox').attr("checked", "checked" );
                $(this).find('.main-input__listing-list__check').addClass('active');
                let itemId = $(this).find('.main-input__listing-list__caption').attr('data-area-value');
                let itemText = $(this).find('.main-input__listing-list__caption').text();
                $('#type').val(function(i, val) {
                    return val + itemId + ',';
                });
                $('#typesList .main-input__select').val(function(i, val) {
                    return val + itemText + ',';
                });
                console.log($(this).parent());
            });
        } else {
            // console.log('has no value');
        }
   
        $(window).bind("load", function() {
            filterChange();
        });
		$('#filter').change(function () {
			filterChange();
			setTimeout(shareFunc, 2000);
        });
        $('.main-input__listing-clear').click(function(){
           $(this).parent().parent().find('.main-input__select').val('');
           $(this).parent().parent().find('.main-input__select-hidden').val('');
        //    console.log( $(this).parent().parent().find('input').val() );
           filterChange();
			setTimeout(shareFunc, 2000);
        });
        $( "input[name='areaRadios']" ).change(function() {
            console.log($( this ).val());
            $('#newArea').val( $(this).val() );
            $('#area').val( $(this).val() );
            $('#newAreaText').val( $(this).val() );
        //    filterChange();
        });
        $('#readioClear').click(function(){
            $(this).parent().find('input').prop('checked', false);
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
				
				let mapFull = document.querySelector('.map-col__map');
				let acfMap = mapFull.querySelector('.acf-map');
				mapFull.style.height = '';
				acfMap.style.height = '';
				console.log('1');

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
				console.log('2');
				let innerHeight = window.innerHeight;
				let sort = document.querySelector('.sort');
				let mapFull = document.querySelector('.map-col__map.left');
				let acfMap = mapFull.querySelector('.acf-map');
				
				console.log(innerHeight);
				console.log(innerHeight - sort.getBoundingClientRect().bottom);
				console.log(mapFull);
				acfMap.style.height = innerHeight - sort.getBoundingClientRect().bottom + 'px';
// 				mapFull.style.height = 900 + 'px';
				console.log('0');
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
				
				let innerHeight = window.innerHeight;
				let sort = document.querySelector('.sort');
				let mapFull = document.querySelector('.map-col__map.full');
				mapFull.style.height = innerHeight - sort.getBoundingClientRect().bottom + 'px';
				console.log('0');
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
		setTimeout(shareFunc, 2000);

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
	});
	
	const filterSort = document.querySelector('.filter-sort');
	
	// sort-select__select

    const sortSelectSelect = document.querySelector('.sort-select__select');
    const sortSelectSelectValue = document.querySelector('.sort-select__select-value');
    const sortSelectList = document.querySelector('.sort-select__select-list');
    const sortSelectListSpan = document.querySelectorAll('.sort-select__select-list span');

    sortSelectSelectValue.addEventListener('click', () => {
        console.log('tut');
        sortSelectList.classList.toggle('active');
    });
    
    sortSelectListSpan.forEach((item, i) => {
        item.addEventListener('click', () => {

            sortSelectSelectValue.innerHTML = item.innerHTML;
            filterSort.value = item.dataset.value;
            filterChange();
            setTimeout(shareFunc, 2000);
        });
    });

    document.addEventListener('click', (e) => {
        if (e.target != sortSelectSelect & e.target != sortSelectSelectValue) {
            sortSelectList.classList.remove('active');
        }
    })

	// sort-select__select
	

	// filter-slider
	
    const shareFunc = () => {
        let share = document.querySelectorAll('.share');
        if (share.length > 0) {
            console.log(share);
            let shareText =  document.querySelectorAll('.share_massage');
            let shareLink =  document.querySelectorAll('.link-share');
            share.forEach((item, i) => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log(i);
                    jQuery(function ($) {
                        var $temp = $("<input>");
                        // var $url = $(location).attr('href');
                        var $url = shareLink[i].getAttribute("href");
                        $("body").append($temp);
                        $temp.val($url).select();
                        document.execCommand("copy");
                        $temp.remove();
                    });
                    shareText[i].style.display = 'flex';
                    setTimeout( () => {shareText[i].style.display = ''}, 3000);
                });
            });
        };	
    };
</script>
<?php
get_footer();