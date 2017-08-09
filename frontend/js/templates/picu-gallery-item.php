<script id="picu-gallery-item" type="text/template">
    <figure class="picu-figure" tabindex="0">
        <div class="picu-imgbox <@= orientation @>">
            <div class="picu-imgbox-inner">
                <a href='#<@= number @>' tabindex="-1"><img src="<@= imagePath_small @>" srcset="<@= imagePath_small_srcset @>" alt="<@= title @>" title="<@= description @>" /></a>
            </div>
        </div>
        <figcaption class="picu-caption">
            <div class="picu-img-title">
                <span class="picu-img-num"><@= number @></span>
                <span class="picu-img-name" title="<@= title @>"><@= title @></span>
            </div>
            <@ if ( picu.poststatus != 'approved' ) { @>
            <div class="picu-select-item">
                <input type="checkbox" name="approved-<@= number @>" id="check<@= number @>" value="<@= imageID @>" tabindex="-1" /> <label for="check<@= number @>" tabindex="-1">
                    <svg viewBox="0 0 100 100"><use xlink:href="#icon_check"></use></svg>
                    <span class="picu-select-label"><?php _e( 'Select image', 'picu' ); ?> <@= number @></span>
                </label>
            </div>
            <@ } @>
        </figcaption>
    </figure><!-- .picu-figure -->
</script>