<?php get_header() ?>

<div class="container">
  <div class="grid grid-cols-12 gap-4">
    <div class="col-span-12">
      <div class="py-8">
        <h1 class="text-5xl text-center"><?= the_title() ?></h1>
        <div class="py-4">
          <?php the_content() ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer() ?>