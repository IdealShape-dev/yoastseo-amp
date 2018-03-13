<?php

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$yform = Yoast_Form::get_instance();
$yform->admin_header( true, 'wpseo_amp', false, 'wpseo_amp_settings' );

?>

	<h2 class="nav-tab-wrapper" id="wpseo-tabs">
		<a class="nav-tab" id="posttypes-tab" href="#top#posttypes"><?php esc_html_e( 'Post types', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="design-tab" href="#top#design"><?php esc_html_e( 'Design', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="analytics-tab" href="#top#analytics"><?php esc_html_e( 'Analytics', 'wordpress-seo' ); ?></a>
	</h2>

	<div class="tabwrapper">

		<div id="posttypes" class="wpseotab">
			<h2><?php esc_html_e( 'Post types that have AMP support', 'wordpress-seo' ); ?></h2>
			<p><?php esc_html_e( 'Generally you\'d want this to be your news post types.', 'wordpress-seo' ); ?><br/>
				<?php esc_html_e( 'Post is enabled by default, feel free to enable any of them.', 'wordpress-seo' ); ?></p>
			<?php

			$post_types = apply_filters( 'wpseo_sitemaps_supported_post_types', get_post_types( array( 'public' => true ), 'objects' ) );

			// Allow specific AMP post type overrides, especially needed for Page support.
			$post_types = apply_filters( 'wpseo_amp_supported_post_types', $post_types );

			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $pt ) {
					$yform->toggle_switch(
						'post_types-' . $pt->name . '-amp',
						array(
							'on'  => __( 'Enabled', 'wordpress-seo' ),
							'off' => __( 'Disabled', 'wordpress-seo' ),
						),
						$pt->labels->name . ' (<code>' . $pt->name . '</code>)'
					);
				}
			}

			if ( ! post_type_supports( 'page', AMP_QUERY_VAR ) ) :
				?>
				<br>
				<strong><?php esc_html_e( 'Please note:', 'wordpress-seo' ); ?></strong>
				<?php esc_html_e( 'Currently pages are not supported by the AMP plugin.', 'wordpress-seo' ); ?>
				<?php
			endif;
			?>
			</p>
		</div>

		<div id="design" class="wpseotab">
			<h3><?php esc_html_e( 'Images', 'wordpress-seo' ); ?></h3>

			<?php
			$yform->media_input( 'amp_site_icon', __( 'AMP icon', 'wordpress-seo' ) );
			?>
			<p class="desc"><?php esc_html_e( 'Must be at least 32px &times; 32px', 'wordpress-seo' ); ?></p>
			<br/>

			<?php
			$yform->media_input( 'default_image', __( 'Default image', 'wordpress-seo' ) );
			?>
			<p class="desc"><?php esc_html_e( 'Used when a post doesn\'t have an image associated with it.', 'wordpress-seo' ); ?>
				<br><?php esc_html_e( 'The image must be at least 696px wide.', 'wordpress-seo' ); ?></p>
			<br/>

			<h3><?php esc_html_e( 'Content colors', 'wordpress-seo' ); ?></h3>

			<?php
			$this->color_picker( 'header-color', __( 'AMP Header color', 'wordpress-seo' ) );
			$this->color_picker( 'headings-color', __( 'Title color', 'wordpress-seo' ) );
			$this->color_picker( 'text-color', __( 'Text color', 'wordpress-seo' ) );
			$this->color_picker( 'meta-color', __( 'Post meta info color', 'wordpress-seo' ) );
			?>
			<br/>

			<h3><?php esc_html_e( 'Links', 'wordpress-seo' ); ?></h3>
			<?php
			$this->color_picker( 'link-color', __( 'Text color', 'wordpress-seo' ) );
			$this->color_picker( 'link-color-hover', __( 'Hover color', 'wordpress-seo' ) );
			?>

			<?php
			$yform->light_switch(
				'underline',
				__( 'Underline', 'wordpress-seo' ),
				array(
					__( 'Underline', 'wordpress-seo' ),
					__( 'No underline', 'wordpress-seo' ),
				)
			);
			?>

			<br/>

			<h3><?php esc_html_e( 'Blockquotes', 'wordpress-seo' ); ?></h3>
			<?php
			$this->color_picker( 'blockquote-text-color', __( 'Text color', 'wordpress-seo' ) );
			$this->color_picker( 'blockquote-bg-color', __( 'Background color', 'wordpress-seo' ) );
			$this->color_picker( 'blockquote-border-color', __( 'Border color', 'wordpress-seo' ) );
			?>
			<br/>

			<h3><?php esc_html_e( 'Extra CSS', 'wordpress-seo' ); ?></h3>
			<?php
			$yform->textarea(
				'extra-css',
				__( 'Extra CSS', 'wordpress-seo' ),
				array(
					'rows' => 5,
					'cols' => 100,
				)
			);
			?>

			<br/>

			<h3><?php printf( esc_html__( 'Extra code in %s', 'wordpress-seo' ), '<code>&lt;head&gt;</code>' ); ?></h3>
			<p><?php printf( esc_html__( 'Only %s and %s tags are allowed, other tags will be removed automatically.', 'wordpress-seo' ), '<code>meta</code>', '<code>link</code>' ); ?></p>
			<?php
			$yform->textarea(
				'extra-head',
				__( 'Extra code', 'wordpress-seo' ),
				array(
					'rows' => 5,
					'cols' => 100,
				)
			);
			?>

		</div>

		<div id="analytics" class="wpseotab">
			<h2><?php esc_html_e( 'AMP Analytics', 'wordpress-seo' ); ?></h2>

			<?php
			if ( class_exists( 'Yoast_GA_Options' ) ) {
				echo '<p>', esc_html__( 'Because your Google Analytics plugin by Yoast is active, your AMP pages will also be tracked.', 'wordpress-seo' ), '<br>';
				$UA = Yoast_GA_Options::instance()->get_tracking_code();
				if ( $UA === null ) {
					esc_html_e( 'Make sure to connect your Google Analytics plugin properly.', 'wordpress-seo' );
				}
				else {
					printf( esc_html__( 'Pageviews will be tracked using the following account: %s.', 'wordpress-seo' ), '<code>' . esc_html( $UA ) . '</code>' );
				}

				echo '</p>';

				echo '<p>', esc_html__( 'Optionally you can override the default AMP tracking code with your own by putting it below:', 'wordpress-seo' ), '</p>';
				$yform->textarea(
					'analytics-extra',
					__( 'Analytics code', 'wordpress-seo' ),
					array(
						'rows' => 5,
						'cols' => 100,
					)
				);
			}
			else {
				echo '<p>', esc_html__( 'Optionally add a valid google analytics tracking code.', 'wordpress-seo' ), '</p>';
				$yform->textarea(
					'analytics-extra',
					__( 'Analytics code', 'wordpress-seo' ),
					array(
						'rows' => 5,
						'cols' => 100,
					)
				);
			}
			?>
		</div>
	</div>

<?php

$yform->admin_footer();
