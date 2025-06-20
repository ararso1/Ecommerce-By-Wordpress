<?php
defined( 'ABSPATH' ) || exit;
$data_struct_course = [
	'urlApi'      => get_rest_url( null, 'lp/v1/admin/tools/search-course' ),
	'dataType'    => 'courses',
	'keyGetValue' => [
		'value'      => 'ID',
		'text'       => '{{post_title}} (#{{ID}})',
		'key_render' => [
			'post_title' => 'post_title',
			'ID'         => 'ID',
		],
	],
	'setting'     => [
		'placeholder' => esc_html__( 'Choose Course', 'learnpress' ),
	],
];

$level_id  = $_GET['edit'];
$courses   = lp_pmpro_get_course_by_level_id( $level_id ) ?? [];
$data_save = [];
foreach ( $courses as $course ) {
	$data_save[] = $course->ID;
}
$collection = new LP_Meta_Box_Select_Field(
	'',
	'',
	$courses,
	[
		'options'           => [],
		'tom_select'        => true,
		'multiple'          => true,
		'multil_meta'       => true,
		'custom_save'       => true,
		'custom_attributes' => [ 'data-struct' => htmlentities2( json_encode( $data_struct_course ) ) ],
		'data-saved'        => $data_save,
	]
);

$collection->id         = '_lp_pmpro_courses';
$count_courses_in_level = (int) LP_PMS_DB::getInstance()->countCoursesOnLevel( $level_id );
?>

<table class="form-table">
	<tbody>
		<tr class="membership_categories">
			<th scope="row" >
				<label>
					<?php
					printf(
						'%s:<br>%s',
						esc_html__( 'Assign courses to level', 'learnpress-paid-membership-pro' ),
						sprintf(
							'%d %s',
							$count_courses_in_level,
							_n( 'course assigned', 'courses assigned', $count_courses_in_level, 'learnpress-paid-membership-pro' )
						)
					);
					?>
				</label>
			</th>
			<td>
				<?php $collection->output( $level_id ); ?>
			</td>
		</tr>
	</tbody>
</table>
