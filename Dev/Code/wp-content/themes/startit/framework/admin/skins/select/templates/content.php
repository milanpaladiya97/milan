<div class="qodef-tabs-content">
    <div class="tab-content">
        <div class="tab-pane fade in active">
            <div class="qodef-tab-content">
                <h2 class="qodef-page-title"><?php echo esc_html($page->title); ?></h2>
                <form method="post" class="qode_ajax_form">
                    <?php wp_nonce_field('qode_ajax_save_nonce', 'qode_ajax_save_nonce'); ?>
                    <div class="qodef-page-form">
                        <?php $page->render(); ?>
                        <?php $this->getAchorsAndSave($page); ?>
                    </div>
                </form>
            </div><!-- close qodef-tab-content -->
        </div>
    </div>
</div> <!-- close div.qodef-tabs-content -->