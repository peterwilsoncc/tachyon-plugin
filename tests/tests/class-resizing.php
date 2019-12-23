<?php
namespace HM\Tachyon\Tests;

use WP_UnitTestCase;

/**
 * Test rescheduling an event is successful.
 *
 * @ticket 64
 */
class Tests_Resizing extends WP_UnitTestCase {

	/**
	 * @var int[] Attachment IDs
	 */
	static $attachment_ids;

	/**
	 * Set up attachments and posts require for testing.
	 *
	 * tachyon.jpg: 1280x719
	 * tachyon-large.jpg: 5312x2988
	 * Photo by Digital Buggu from Pexels
	 * @link https://www.pexels.com/photo/0-7-rpm-171195/
	 */
	static public function wpSetUpBeforeClass( $factory ) {
		self::$attachment_ids['tachyon'] = $factory->attachment->create_upload_object(
			realpath( __DIR__ . '/../data/tachyon.jpg')
		);

		self::$attachment_ids['tachyon-large'] = $factory->attachment->create_upload_object(
			realpath( __DIR__ . '/../data/tachyon-large.jpg')
		);
	}

	/**
	 * Set up a new image size created after upload.
	 *
	 * This is done on setUp as image sizes are reset as part of the
	 * test suite's standard tearDown() procedure.
	 */
	function setUp() {
		parent::setUp();

		add_image_size(
			'oversized',
			2000,
			1000
		);
	}

	/**
	 * Test URLs are parsed correctly.
	 *
	 * @dataProvider data_filtered_url
	 */
	function test_filtered_url( $file, $size, $valid_urls ) {
		$valid_urls = (array) $valid_urls;
		$actual_src = wp_get_attachment_image_src( self::$attachment_ids[ $file ], $size );
		$actual_url = $actual_src[0];

		$this->assertContains( $actual_url, $valid_urls, "The resized image is expected to be {$actual_src[1]}x{$actual_src[2]}" );
	}

	/**
	 * Data provider for `test_filtered_url()`.
	 *
	 * Only the filename and querystring are stored as the
	 *
	 * return array[] {
	 *     $file       string The basename of the uploaded file to tests against.
	 *     $size       string The image size requested.
	 *     $valid_urls array  Valid Tachyon URLs for resizing.
	 * }
	 */
	function data_filtered_url() {
		return [
			[
				'tachyon',
				'thumb',
				[
					'http://tachy.on/u/tachyon.jpg?resize=150,150',
				],
			],
			[
				'tachyon',
				'thumbnail',
				[
					'http://tachy.on/u/tachyon.jpg?resize=150,150',
				],
			],
			[
				'tachyon',
				'medium',
				[
					'http://tachy.on/u/tachyon.jpg?fit=300,169',
					'http://tachy.on/u/tachyon.jpg?resize=300,169',
					'http://tachy.on/u/tachyon.jpg?fit=300,300',
				],
			],
			[
				'tachyon',
				'medium_large',
				[
					'http://tachy.on/u/tachyon.jpg?fit=768,431',
					'http://tachy.on/u/tachyon.jpg?resize=768,431',
					'http://tachy.on/u/tachyon.jpg?w=768',
					'http://tachy.on/u/tachyon.jpg?w=768&h=431',
				],
			],
			[
				'tachyon',
				'large',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1024,575',
					'http://tachy.on/u/tachyon.jpg?resize=1024,575',
					'http://tachy.on/u/tachyon.jpg?fit=1024,1024',
					'http://tachy.on/u/tachyon.jpg?w=1024&h=575',
				],
			],
			[
				'tachyon',
				'full',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
			[
				'tachyon',
				'oversized',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
			[
				'tachyon-large',
				'thumb',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=150,150',
				],
			],
			[
				'tachyon-large',
				'thumbnail',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=150,150',
				],
			],
			[
				'tachyon-large',
				'medium',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=300,169',
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=300,169',
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=300,300',
				],
			],
			[
				'tachyon-large',
				'medium_large',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=768,432',
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=768,432',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=768',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=768&h=432',
				],
			],
			[
				'tachyon-large',
				'large',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=1024,575',
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=1024,575',
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=1024,1024',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=1024&h=575',
				],
			],
			[
				'tachyon-large',
				'full',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=2560,1440',
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=2560,1440',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=2560',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=2560&h=1440',
					'http://tachy.on/u/tachyon-large-scaled.jpg',
				],
			],
			[
				'tachyon-large',
				'oversized',
				[
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=1778,1000',
					'http://tachy.on/u/tachyon-large-scaled.jpg?resize=1778,1000',
					'http://tachy.on/u/tachyon-large-scaled.jpg?fit=2000,1000',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=1778',
					'http://tachy.on/u/tachyon-large-scaled.jpg?w=1778&h=1000',
					'http://tachy.on/u/tachyon-large-scaled.jpg',
				],
			],
		];
	}

	/**
	 * Extract the first src attribute from the given HTML.
	 *
	 * There should only ever be one image in the content so regex
	 * can be dropped in favour of strpos techniques for getting the
	 * src of an image.
	 *
	 * @param $html string HTML containing an image tag.
	 * @return string The first `src` attribute within the first image tag.
	 */
	function get_src_from_html( $html ) {
		if (
			strpos( $html, '<img' ) === false ||
			strpos( $html, '>' ) === false ||
			strpos( $html, 'src=' ) === false ||
			strpos( $html, '"' ) === false
		) {
			return false;
		}

		$html = substr( $html, strpos( $html, '<img' ) );
		$html = substr( $html, 0, strpos( $html, '>' ) + 1 );
		$html = substr( $html, strpos( $html, 'src="' ) + 5 );
		$html = substr( $html, 0, strpos( $html, '"' ) );
		return $html;
	}

	/**
	 * Test image tags passed as part of the content.
	 *
	 * @dataProvider data_content_filtering
	 */
	function test_content_filtering( $file, $content, $valid_urls ) {
		$valid_urls = (array) $valid_urls;
		$attachment_id = self::$attachment_ids[ $file ];
		$content = str_replace( '%%ID%%', $attachment_id, $content );
		$post_id = $this->factory()->post->create( [
			'post_content' => $content,
		] );
		$this->go_to( get_permalink( $post_id ) );
		the_post();

		$the_content = get_echo( 'the_content' );
		$actual_src = $this->get_src_from_html( $the_content );

		$this->assertContains( $actual_src, $valid_urls, 'The resized image is expected to be ' . implode( ' or ', $valid_urls ) );
	}

	/**
	 * Data provider for test_content_filtering.
	 *
	 * return array[] {
	 *     $file         string The basename of the uploaded file to tests against.
	 *     $content      string The content being filtered. `%%ID%%` is replaced with the attachment ID during the test.
	 *     $valid_urls   array  Valid Tachyon URLs for resizing.
	 * }
	 */
	function data_content_filtering() {
		return [
			// Classic editor formatted image tags.
			[
				'tachyon',
				'<p><img class="alignnone wp-image-%%ID%% size-thumb" src="http://example.org/wp-content/uploads/tachyon-150x150.jpg" alt="" width="150" height="150" /></p>',
				[
					'http://tachy.on/u/tachyon.jpg?resize=150,150',
				],
			],
			[
				'tachyon',
				'<p><img class="alignnone wp-image-%%ID%% size-medium" src="http://example.org/wp-content/uploads/tachyon-300x169.jpg" alt="" width="300" height="169" /></p>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=300,169',
					'http://tachy.on/u/tachyon.jpg?resize=300,169',
					'http://tachy.on/u/tachyon.jpg?fit=300,300',
				],
			],
			[
				'tachyon',
				'<p><img class="alignnone wp-image-%%ID%% size-large" src="http://example.org/wp-content/uploads/tachyon-1024x575.jpg" alt="" width="1024" height="575" /></p>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1024,575',
					'http://tachy.on/u/tachyon.jpg?resize=1024,575',
					'http://tachy.on/u/tachyon.jpg?fit=1024,1024',
					'http://tachy.on/u/tachyon.jpg?w=1024&h=1024',
				],
			],
			[
				'tachyon',
				'<p><img class="alignnone wp-image-%%ID%% size-full" src="http://example.org/wp-content/uploads/tachyon.jpg" alt="" width="1280" height="719" /></p>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
			[
				'tachyon',
				'<p><img class="alignnone wp-image-%%ID%% size-oversized" src="http://example.org/wp-content/uploads/tachyon.jpg" alt="" width="1280" height="719" /></p>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
			// Block editor formatted image tags.
			[
				'tachyon',
				'<figure class="wp-block-image size-thumbnail"><img src="http://example.org/wp-content/uploads/tachyon-150x150.jpg" alt="" class="wp-image-%%ID%%"></figure>',
				[
					'http://tachy.on/u/tachyon.jpg?resize=150,150',
				],
			],
			[
				'tachyon',
				'<figure class="wp-block-image size-medium"><img src="http://example.org/wp-content/uploads/tachyon-300x169.jpg" alt="" class="wp-image-%%ID%%"></figure>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=300,169',
					'http://tachy.on/u/tachyon.jpg?resize=300,169',
					'http://tachy.on/u/tachyon.jpg?fit=300,300',
				],
			],
			[
				'tachyon',
				'<figure class="wp-block-image size-large"><img src="http://example.org/wp-content/uploads/tachyon-1024x575.jpg" alt="" class="wp-image-%%ID%%"></figure>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1024,575',
					'http://tachy.on/u/tachyon.jpg?resize=1024,575',
					'http://tachy.on/u/tachyon.jpg?fit=1024,1024',
					'http://tachy.on/u/tachyon.jpg?w=1024&h=1024',
				],
			],
			[
				'tachyon',
				'<figure class="wp-block-image size-full"><img src="http://example.org/wp-content/uploads/tachyon.jpg" alt="" class="wp-image-%%ID%%"></figure>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
			[
				'tachyon',
				'<figure class="wp-block-image size-oversized"><img src="http://example.org/wp-content/uploads/tachyon.jpg" alt="" class="wp-image-%%ID%%"></figure>',
				[
					'http://tachy.on/u/tachyon.jpg?fit=1280,719',
					'http://tachy.on/u/tachyon.jpg?resize=1280,719',
					'http://tachy.on/u/tachyon.jpg?w=1280',
					'http://tachy.on/u/tachyon.jpg?w=1280&h=719',
					'http://tachy.on/u/tachyon.jpg',
				],
			],
		];
	}
}
