<?php
/**
 * Template for displaying content of archive courses page.
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.2
 */

defined( 'ABSPATH' ) || exit;

global $post, $wp_query, $lp_tax_query;

$show_description = get_theme_mod( 'thim_learnpress_cate_show_description' );
$show_desc        = ! empty( $show_description ) ? $show_description : '';
$cat_desc         = term_description();

$total   = $wp_query->found_posts;
$message = '';

if ( $total == 0 ) {
	$message = '<p class="message message-error">' . esc_html__( 'No course found!', 'eduma' ) . '</p>';
	$index   = esc_html__( 'There are no available courses!', 'eduma' );
} elseif ( $total == 1 ) {
	$index = esc_html__( 'Showing only one result', 'eduma' );
} else {
	$courses_per_page = absint( LP_Settings::get_option( 'archive_course_limit', 6 ) );
	$paged            = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;

	$from = 1 + ( $paged - 1 ) * $courses_per_page;
	$to   = ( $paged * $courses_per_page > $total ) ? $total : $paged * $courses_per_page;

	if ( $from == $to ) {
		$index = sprintf(
			esc_html__( 'Showing last course of %s results', 'eduma' ),
			$total
		);
	} else {
		$index = sprintf(
			esc_html__( 'Showing %1$s-%2$s of %3$s results', 'eduma' ),
			$from,
			$to,
			$total
		);
	}
}

$layout_setting = get_theme_mod( 'thim_learnpress_cate_layout_grid', true );
if ( $layout_setting == 'list_courses' ) {
	$set_layout = 'thim-course-list';
} else {
	$set_layout = 'thim-course-grid';
}

$default_order = apply_filters(
	'learn-press/courses/order-by/values',
	array(
		'post_date'       => esc_html__( 'Newly published', 'eduma' ),
		'post_title'      => esc_html__( 'Title a-z', 'learnpress' ),
		'post_title_desc' => esc_html__( 'Title z-a', 'learnpress' ),
		'price'           => esc_html__( 'Price high to low', 'learnpress' ),
		'price_low'       => esc_html__( 'Price low to high', 'learnpress' ),
		'popular'         => esc_html__( 'Popular', 'learnpress' ),
	)
);

/**
 * @since 4.0.0
 *
 * @see   LP_Template_General::template_header()
 */
do_action( 'learn-press/template-header' );

/**
 * thim_wrapper_loop_start hook
 *
 * @hooked thim_wrapper_loop_end - 1
 * @hooked thim_wapper_page_title - 5
 * @hooked thim_wrapper_loop_start - 30
 */

do_action( 'thim_wrapper_loop_start' );
/**
 * LP Hook
 */
do_action( 'learn-press/before-main-content' );

do_action( 'lp/template/archive-course/description' );

$thim_course_sort = LP_Helper::sanitize_params_submitted( $_REQUEST['order_by'] ?? '' );
?>
	<div class="lp-content-area">

		<div class="thim-course-top switch-layout-container<?php if ( $show_desc && $cat_desc ) {
			echo ' has_desc';
		} ?>">
			<div class="thim-course-switch-layout switch-layout">
				<a href="javascript:;"
				   class="list switchToGrid<?php echo ( $set_layout == 'thim-course-grid' ) ? ' switch-active' : ''; ?>">
					<i class="<?php echo eduma_font_icon( 'th-large' ); ?>"></i>
				</a>
				<a href="javascript:;"
				   class="grid switchToList<?php echo ( $set_layout == 'thim-course-list' ) ? ' switch-active' : ''; ?>">
					<i class="<?php echo eduma_font_icon( 'list' ); ?>"></i>
				</a>
			</div>
			<div class="course-index">
				<span class="courses-page-result"><?php echo( $index ); ?></span>
			</div>
			<?php if ( get_theme_mod( 'thim_display_course_sort', true ) ) : ?>
				<div class="thim-course-order">
					<select name="orderby">
						<?php
						foreach ( $default_order as $k => $v ) {
							$selected = '';
							if ( $k === $thim_course_sort ) {
								$selected = 'selected';
							}
							echo '<option value="' . esc_attr( $k ) . '" ' . $selected . '>' . ( $v ) . '</option>';
						}
						?>
					</select>
				</div>
			<?php endif; ?>
			<?php if ( get_theme_mod( 'thim_display_course_filter', false ) ) { ?>
				<button class="filter-courses-effect filter-mobile-btn">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path
							d="M8.37013 7.79006C8.42013 8.22006 8.73013 8.55006 9.17013 8.63006C9.24013 8.64006 9.31013 8.65006 9.38013 8.65006C9.74013 8.65006 10.0701 8.46006 10.2401 8.15006C10.2401 8.15006 10.3701 7.93006 10.3701 7.86006V6.83006H21.3701C21.4801 6.83006 21.8501 6.61006 21.9301 6.52006C22.1301 6.31006 22.2301 5.99006 22.1801 5.68006C22.1401 5.36006 21.9601 5.10006 21.7001 4.95006C21.6801 4.94006 21.3401 4.81006 21.2801 4.81006H10.3701V3.77006C10.3701 3.64006 10.1101 3.30006 10.0601 3.25006C9.80013 3.01006 9.39013 2.94006 9.03013 3.07006C8.68013 3.19006 8.44013 3.47006 8.39013 3.81006C8.34013 4.16006 8.36013 4.61006 8.37013 5.05006C8.37013 5.25006 8.39013 5.44006 8.39013 5.61006C8.39013 5.78006 8.39013 5.96006 8.37013 6.16006C8.35013 6.71006 8.33013 7.34006 8.37013 7.80006V7.79006Z"
							fill="#098CE9"></path>
						<path
							d="M21.3701 17.5401H10.3701V16.5101C10.3701 16.4501 10.2201 16.1701 10.2201 16.1601C9.99013 15.8101 9.57013 15.6601 9.14013 15.7501C8.72013 15.8501 8.42013 16.1701 8.37013 16.5801C8.34013 16.9301 8.35013 17.3401 8.37013 17.7401C8.37013 17.9501 8.38013 18.1501 8.38013 18.3401C8.38013 18.5001 8.38013 18.6801 8.36013 18.8801C8.34013 19.4601 8.31013 20.1201 8.38013 20.5701C8.42013 20.8601 8.61013 21.1101 8.89013 21.2601C9.05013 21.3401 9.22013 21.3801 9.39013 21.3801C9.56013 21.3801 9.71013 21.3401 9.85013 21.2701C10.0201 21.1901 10.3701 20.8201 10.3701 20.6101V19.5801H21.2801C21.3401 19.5801 21.6801 19.4501 21.7001 19.4401C21.9601 19.2901 22.1301 19.0201 22.1701 18.7101C22.2101 18.4001 22.1201 18.0801 21.9101 17.8601C21.8601 17.8101 21.4801 17.5501 21.3501 17.5501L21.3701 17.5401Z"
							fill="#098CE9"></path>
						<path
							d="M14.3401 9.45006C14.0301 9.31006 13.7001 9.32006 13.4301 9.49006C13.1301 9.67006 12.9201 10.0201 12.8901 10.4201C12.8101 11.4001 12.8301 13.0001 12.8901 13.9301C12.9301 14.3901 13.1701 14.7801 13.5201 14.9501C13.6401 15.0101 13.7701 15.0301 13.9001 15.0301C14.1101 15.0301 14.3101 14.9601 14.5101 14.8201C14.6601 14.7201 14.9201 14.3701 14.9201 14.1701V13.1901H21.4001C21.4001 13.1901 21.4501 13.1901 21.6901 13.0701C21.9901 12.9001 22.1801 12.5901 22.1901 12.2301C22.2001 11.8701 22.0301 11.5401 21.7401 11.3601C21.7201 11.3501 21.4601 11.2101 21.3901 11.2101H14.9101V10.2201C14.9101 9.96006 14.5501 9.56006 14.3301 9.46006L14.3401 9.45006Z"
							fill="#098CE9"></path>
						<path
							d="M2.84013 13.1801H11.3801C11.8701 13.0601 12.2001 12.6701 12.2101 12.2001C12.2101 11.7301 11.9201 11.3401 11.4301 11.2001H2.77013C2.23013 11.3501 2.00013 11.8201 2.00013 12.2201C2.01013 12.7001 2.33013 13.0801 2.83013 13.1901L2.84013 13.1801Z"
							fill="#098CE9"></path>
						<path
							d="M2.84013 6.82006H6.82013C7.40013 6.69006 7.66013 6.22006 7.65013 5.80006C7.65013 5.39006 7.38013 4.92006 6.77013 4.81006C6.25013 4.84006 5.66013 4.81006 5.09013 4.78006C4.35013 4.74006 3.58013 4.70006 2.92013 4.79006C2.31013 4.90006 2.02013 5.36006 2.00013 5.79006C1.98013 6.21006 2.23013 6.69006 2.83013 6.83006L2.84013 6.82006Z"
							fill="#098CE9"></path>
						<path
							d="M6.86013 17.5501H2.82013C2.23013 17.6901 1.98013 18.1801 2.00013 18.5901C2.02013 19.0101 2.31013 19.4701 2.92013 19.5601C3.22013 19.6001 3.54013 19.6201 3.87013 19.6201C4.23013 19.6201 4.60013 19.6001 4.96013 19.5901C5.55013 19.5601 6.15013 19.5401 6.69013 19.5901H6.71013C7.31013 19.5101 7.60013 19.0601 7.63013 18.6401C7.66013 18.2201 7.43013 17.7201 6.84013 17.5701L6.86013 17.5501Z"
							fill="#098CE9"></path>
					</svg>
				</button>
			<?php } ?>
			<div class="courses-searching">
				<form class="search-courses" method="get"
					  action="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>">
					<input type="text" value="<?php echo esc_attr( LP_Request::get( 'c_search' ) ); ?>" name="c_search"
						   placeholder="<?php esc_attr_e( 'Search our courses', 'eduma' ); ?>"
						   class="form-control course-search-filter" autocomplete="off"/>
					<input type="hidden" value="course" name="ref"/>
					<input type="hidden" name="post_type" value="lp_course">
					<input type="hidden" name="taxonomy"
						   value="<?php echo esc_attr( get_queried_object()->taxonomy ?? $_GET['taxonomy'] ?? '' ); ?>">
					<input type="hidden" name="term_id"
						   value="<?php echo esc_attr( get_queried_object()->term_id ?? $_GET['term_id'] ?? '' ); ?>">
					<input type="hidden" name="term"
						   value="<?php echo esc_attr( get_queried_object()->slug ?? $_GET['term'] ?? '' ); ?>">
					<button type="submit" aria-label="search"><i class="fab fa-sistrix"></i></button>
					<span class="widget-search-close"></span>
				</form>
			</div>
		</div>

		<?php
		/**
		 * LP Hook
		 */
		// do_action( 'learn-press/before-courses-loop' );
		LearnPress::instance()->template( 'course' )->begin_courses_loop();
		?>
		<?php if ( $show_desc && $cat_desc ) { ?>
			<div class="desc_cat">
				<?php echo $cat_desc; ?>
			</div>
		<?php } ?>

		<div id="thim-course-archive" class="<?php echo $set_layout; ?>">
			<ul class="learn-press-courses">
				<?php
				if ( version_compare( LEARNPRESS_VERSION, '4.1.6.5', '=' )
					|| ( version_compare( LEARNPRESS_VERSION, '4.1.6.6-beta-1', '>=' )
						&& LP_Settings_Courses::is_ajax_load_courses()
						&& ! LP_Settings_Courses::is_no_load_ajax_first_courses() )
				) {
					echo '<div class="lp-archive-course-skeleton" style="width:100%">';
					echo lp_skeleton_animation_html( 10, '100%', 'height:20px', 'width:100%' );
					echo '<div class="cssload-loading"><i></i><i></i><i></i><i></i></div>';
					echo '</div>';
				} else {
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							learn_press_get_template_part( 'content', 'course' );
						endwhile;
						wp_reset_postdata();
					else :
						echo $message;
					endif;

					if ( version_compare( LEARNPRESS_VERSION, '4.1.6.6-beta-1', '>=' ) && LP_Settings_Courses::is_ajax_load_courses() ) {
						echo '<div class="lp-archive-course-skeleton no-first-load-ajax" style="width:100%; display: none">';
						echo lp_skeleton_animation_html( 10, 'random', 'height:20px', 'width:100%' );
						echo '<div class="cssload-loading"><i></i><i></i><i></i><i></i></div>';
						echo '</div>';
					}
				}
				?>
			</ul>
		</div>

		<?php
		LearnPress::instance()->template( 'course' )->end_courses_loop();
		/**
		 * @since 4.0.0
		 */
		do_action( 'learn-press/after-courses-loop' );

		wp_reset_postdata();
		?>
	</div>

<?php
/**
 * LP Hook
 */
do_action( 'learn-press/after-main-content' );
?>

<?php
/**
 * thim_wrapper_loop_end hook
 *
 * @hooked thim_wrapper_loop_end - 10
 * @hooked thim_wrapper_div_close - 30
 */
do_action( 'thim_wrapper_loop_end' );
/**
 * @since 4.0.0
 *
 * @see   LP_Template_General::template_footer()
 */
do_action( 'learn-press/template-footer' );
