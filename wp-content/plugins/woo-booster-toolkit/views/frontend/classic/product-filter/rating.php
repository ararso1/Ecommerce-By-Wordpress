<?php
if (!isset($data)) {
	return;
}
$show_title = $data['show_title'] ?? 'yes';
$show_count = $data['show_count'] ?? 'yes';

use WCBT\Models\ProductModel;

$value = $_GET['rating'] ?? '';
if (empty($value) || !is_string($value)) {
	$value = '';
}

?>
<div class="rating wrapper <?php if (!empty($data['extra_class'])) {
								echo $data['extra_class'];
							}; ?>">
	<?php if ($show_title == 'yes') { ?>
		<div class="item-filter-heading"><?php esc_html_e('Rating', 'wcbt'); ?></div>
	<?php } ?>
	<ul class="wrapper-content">
        <li>
            <input type="radio" name="rating" value="all" <?php checked('all', $value, true); ?>>
            <span><?php esc_html_e('All Rating', 'wcbt');?></span>
        </li>
		<?php
		for ($j = 4; $j > 0; $j--) {
			?>
			<li>
				<input type="radio" name="rating" value="<?php echo $j; ?>" <?php checked($j, $value, true); ?>>
				<?php
				$total = ProductModel::get_product_total_by_rating($j);
				?>
				<div class="star">
					<?php
					for ($i = 0; $i < 5; $i++) {
						if($i < $j){
							echo '<i class="star-solid"></i>';
						}else{
							echo '<i class="star-regular"></i>';
						}
					}
					?>
                    <span><?php esc_html_e('& Up', 'wcbt');?></span>
				</div>
				<?php if ($show_count == 'yes') {
				?>
					<span class="product-count">(<?php echo esc_html($total); ?>)</span>
				<?php } ?>
			</li>
		<?php
		}
		?>
	</ul>
</div>
