<?php defined( 'ABSPATH' ) || die( 'Cheatin’ uh?' ); ?>

<h1 class="screen-reader-text"><?php _e( 'Bulk Optimization', 'imagify' ); ?> – Imagify <?php echo IMAGIFY_VERSION; ?></h1>

<div class="imagify-title">

	<p class="imagify-logo-block">
		<span class="imagify-lb-icon">
			<svg class="icon icon-bulk" viewBox="0 0 38 36" width="38" height="36" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="m233.09 177.21l-5.52 10.248c-.08.145-.018.272-.023.388-.074.193-.033.4-.033.619v21.615c0 .952.601 1.429 1.552 1.429h33.897c.952 0 1.962-.478 1.962-1.429v-21.615c0-.487-.323-.925-.649-1.24l-5.623-9.976c-.405-.726-1.202-1.179-2.034-1.182l-21.486-.068c-.849 0-1.64.464-2.043 1.211m30.424 32.869c0 .173-.378.018-.551.018h-33.897c-.172 0-.14.155-.14-.018v-21.576l33.961-.281c.066.008.186.09.263.128.054.027.205.049.258.073.002.014.106.027.106.041v21.615m-6.153-32.11l4.91 8.835h-14.992v-9.354l9.306.045c.322.001.619.192.776.474m-11.494-.523v9.358h-16.306l4.773-8.892c.155-.289.456-.484.787-.484l10.746.018m7.06 17.12c0 .39-.316.706-.706.706h-12.706c-.39 0-.706-.316-.706-.706 0-.39.316-.706.706-.706h12.706c.39 0 .706.316.706.706" transform="translate(-227-176)" fill="#40B1D0"/></g></svg>
		</span>
		<span class="imagify-lb-text">
			<img width="139" height="16" alt="Imagify" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" class="imagify-logo" />
			<?php _e( 'Bulk Optimization', 'imagify' ); ?>
		</span>
	</p>

	<?php $this->print_template( 'part-documentation-link' ); ?>
</div>
<?php
