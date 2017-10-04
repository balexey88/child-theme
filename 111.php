<?php
/*
Template Name: My page template
*/


get_header(); ?>

<div class="wrap">
	<h2>Звоните мне: <?=get_post_meta(get_the_ID(), 'my_phone', true);?></h2>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/page/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<pre>
<?php
var_dump($_POST);

$to = 'balexey88@gmail.com';
$subject = 'Новая отправка с сайта';
$message = 'Name: ' . $_POST['my_name'] . "\n";
$message .= 'Email: ' . $_POST['my_email'] . "\n";
$message .= 'Text: ' . $_POST['text'] . "\n";

wp_mail($to, $subject, $message);
?>
</pre>

<form method="post">
  Name: <input type="text" name="my_name"><br>
  Email: <input type="text" name="my_email"><br>
  Text: <textarea name="text"></textarea>

  <input type="submit" value="Отправить">
</form>

<?php get_footer();
