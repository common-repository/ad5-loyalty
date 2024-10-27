<?php
    $message = $this->get_message();
?>

<div class="ad5-loyalty-modal-base <?php if ( $message ) { echo 'ad5-loyalty-show'; } ?>">

<div class="ad5-loyalty-modal-background ad5-loyalty-modal-close">&nbsp;</div>

<?php if ( $message ): ?>
<div class="ad5-loyalty-modal-wrap ad5-loyalty-show">
    <?php if ( ! empty( $message['header'] ) ): ?>
    <div class="ad5-loyalty-modal-header"><?php echo $message['header']; ?></div>
    <?php endif; ?>
    <div class="ad5-loyalty-modal-body">
        <?php if ( ! empty( $message['success'] ) ): ?>
        <div class="ad5-loyalty-form-message-success ad5-loyalty-show"><?php echo $message['success']; ?></div>
        <?php endif; ?>
        <?php if ( ! empty( $message['error'] ) ): ?>
        <div class="ad5-loyalty-form-message-error ad5-loyalty-show"><?php echo $message['error']; ?></div>
        <?php endif; ?>
        <?php if ( ! empty( $message['body'] ) ): ?>
        <?php echo $message['body']; ?>
        <?php endif; ?>
        <div class="ad5-loyalty-modal-button-bottom"><button type="button" class="ad5-loyalty-modal-close ad5-loyalty-button-style-secondary"><?php $this->e('Close'); ?></button></div>
        </div>
        <div class="ad5-loyalty-modal-button-corner"><button type="button" class="ad5-loyalty-modal-close">×</button></div>
</div>
<?php endif; ?>

<div class="ad5-loyalty-modal-wrap" id="ad5-loyalty-signin">
    <div class="ad5-loyalty-modal-header"><?php $this->e( 'Sign In' ); ?></div>
    <div class="ad5-loyalty-modal-body">

    <div class="ad5-loyalty-form-message-success"></div>
    <div class="ad5-loyalty-form-message-error ad5-loyalty-form-error" data-name="error_global"></div>

    <form action="#" class="ad5-loyalty-ajax-form">
    <?php wp_nonce_field( 'ad5-loyalty-signin' ); ?>
    <input type="hidden" name="process" value="signin">
    <dl class="ad5-loyalty-form-list">
    <dt><?php $this->e( 'Email' ); ?><span class="ad5-loyalty-form-error" data-name="user_email"></span></dt>
    <dd><input type="text" name="user_email" class="ad5-loyalty-form-input ad5-loyalty-form-input-full"></dd>
    <dt><?php $this->e( 'Password' ); ?><span class="ad5-loyalty-form-error" data-name="user_pass"></span></dt>
    <dd><input type="password" name="user_pass" class="ad5-loyalty-form-input ad5-loyalty-form-input-full"></dd>
    </dl>
    <div class="ad5-loyalty-form-action"><button type="submit" class="ad5-loyalty-button-style-primary ad5-loyalty-button-submit"><?php $this->e( 'Sign In' ); ?></button></div>
    </form>

    </div>
    <div class="ad5-loyalty-modal-button-corner"><button type="button" class="ad5-loyalty-modal-close">×</button></div>
</div>

<div class="ad5-loyalty-modal-wrap" id="ad5-loyalty-register">
    <div class="ad5-loyalty-modal-header"><?php $this->e( 'Sign Up' ); ?></div>
    <div class="ad5-loyalty-modal-body">

    <div class="ad5-loyalty-form-message-success"></div>
    <div class="ad5-loyalty-form-message-error ad5-loyalty-form-error" data-name="error_global"></div>

    <form action="#" class="ad5-loyalty-ajax-form">
    <?php wp_nonce_field( 'ad5-loyalty-register' ); ?>
    <input type="hidden" name="process" value="register">
    <dl class="ad5-loyalty-form-list">
    <dt><?php $this->e( 'Your Name' ); ?><span class="ad5-loyalty-form-error" data-name="nickname"></span></dt>
    <dd><input type="text" name="nickname" class="ad5-loyalty-form-input ad5-loyalty-form-input-full"></dd>
    <dt><?php $this->e( 'Email' ); ?><span class="ad5-loyalty-form-error" data-name="user_email"></span></dt>
    <dd><input type="text" name="user_email" class="ad5-loyalty-form-input ad5-loyalty-form-input-full"></dd>
    <dt><?php $this->e( 'Password' ); ?><span class="ad5-loyalty-form-error" data-name="user_pass"></span></dt>
    <dd><input type="password" name="user_pass" class="ad5-loyalty-form-input ad5-loyalty-form-input-full"></dd>
    </dl>
    <div class="ad5-loyalty-form-action"><button type="submit" class="ad5-loyalty-button-style-primary ad5-loyalty-button-submit"><?php $this->e( 'Sign Up' ); ?></button></div>
    </form>

    </div>
    <div class="ad5-loyalty-modal-button-corner"><button type="button" class="ad5-loyalty-modal-close">×</button></div>
</div>

<?php do_action( 'ad5-loyalty-modal-html' ); ?>

</div>

<style>
.ad5-loyalty-modal-base {display: none; position: fixed; left:0; top:0; z-index:10000; width:100%; height:100%;}
.ad5-loyalty-modal-background {position: absolute; left:0; top:0; z-index:10001; width:100%; height:100%; background: rgba(0, 0, 0, 0.5);}
.ad5-loyalty-modal-wrap {display: none; position: absolute; left: 0; top:0; right:0; bottom:0; z-index:10002; box-sizing:border-box; margin:auto; padding:20px; background: #FFF;}
.ad5-loyalty-modal-header {height:40px; line-height:20px; font-size:15px;}
.ad5-loyalty-modal-header {box-sizing:border-box; margin-bottom:20px; padding:10px 15px; background: #000; color:#FFF;}
.ad5-loyalty-modal-button-bottom {position: absolute; left:0; bottom:20px; z-index:10003; width:100%; text-align: center;}
.ad5-loyalty-modal-button-bottom button {width:60%; text-align: center; font-size:18px;}
.ad5-loyalty-modal-button-corner {position: absolute; right: -15px; top: -15px;}
.ad5-loyalty-modal-button-corner button {width:30px; height:30px; padding:0px; border: 1px solid #888; text-align:center; line-height:30px; border-radius:15px; background: #FFF; font-size:20px;}
.ad5-loyalty-button-submit {}
.ad5-loyalty-button-disabled {opacity: 0.5;}
.ad5-loyalty-form-list {margin-bottom: 20px;}
.ad5-loyalty-form-list dt {margin:0 0 10px; padding:0; font-size:14px; font-weight:bold;}
.ad5-loyalty-form-list dt span {margin-left:10px;}
.ad5-loyalty-form-list dd {margin:0 0 10px; padding:0;}
.ad5-loyalty-form-input {box-sizing:border-box; height:30px; padding:5px; line-heihgt:20px; font-size:16px;}
.ad5-loyalty-form-input-full {width: 100%;}
.ad5-loyalty-form-action {text-align: center;}
.ad5-loyalty-form-action button {width:60%; text-align: center; font-size:18px;}
.ad5-loyalty-form-error {display:none; color:#F00;}
.ad5-loyalty-form-message-success {display:none; margin-bottom:20px; padding:5px 10px; border:1px solid #0A0; color:#0A0; background:#EFE;}
.ad5-loyalty-form-message-error {display:none; margin-bottom:20px; padding:5px 10px; border:1px solid #F00; color:#F00; background:#FEE;}
.ad5-loyalty-show {display: block;}

@media (min-width: 641px) {
	.ad5-loyalty-modal-wrap {width: 600px; height:400px;}
}
@media (max-width: 640px) {
	.ad5-loyalty-modal-wrap {width: 90%; height:80%; bottom:10%;}
}
</style>

<script>
(function($){
    //modal open
    $(document).on('click', '.ad5-loyalty-button', function(){
        var target = $(this).attr('href');
        if ( ! target ) {
            var target = $(this).data('href');
        }
        if ( target && $(target).length ) {
            $('.ad5-loyalty-form-error').hide();
            $('.ad5-loyalty-form-message-error').hide();
            $('.ad5-loyalty-form-message-success').hide();
            $('.ad5-loyalty-modal-base').show();
            $(target).show();
        }
        var data = $(this).data();
        Object.keys(data).forEach(function(key){
            var input = $(target).find('[name=' + key + ']');
            if (input.length) {
                input.val( data[key] );
            }
        });
        return false;
    });

    //modal close
    $(document).on('click', '.ad5-loyalty-modal-close', function(){
        $('.ad5-loyalty-modal-base').hide();
        $('.ad5-loyalty-modal-wrap').hide();
    });

    //form submit (ajax)
    $('.ad5-loyalty-ajax-form').submit(function(){
        $('.ad5-loyalty-button-submit').prop('disabled', true).addClass('ad5-loyalty-button-disabled');
        $('.ad5-loyalty-form-error').hide();
        $('.ad5-loyalty-form-message-error').hide();
        $('.ad5-loyalty-form-message-success').hide();
		var elements = $(this).serializeArray();
		var data = {};
		elements.forEach(function (element) {
			data[ element.name ] = element.value;
		});

		$.ajax({
			type: 'POST',
			url: '<?php echo admin_url( 'admin-ajax.php'); ?>',
			data: {
				action : 'ad5_loyalty_front',
                dataType : 'json',
				data : data
			},
			success: function( response ) {
                if ( ! response.valid ) {
                    $('.ad5-loyalty-form-message-error').html( '<?php $this->e( 'An error occured while processing your request' ); ?>' ).show();
                } else {
                    if ( response.action ) {
                        if (location.search) {
                            var redirect = location.pathname + location.search + "&ad5_loyalty_action=" + response.action;
                        } else {
                            var redirect = location.pathname + "?ad5_loyalty_action=" + response.action;
                        }
                        location.href = redirect;
                    } else {
                        if ( response.success ) {
                            if ( response.message ) {
                                var message = response.message;
                            } else {
                                var message = '<?php $this->e( 'Request complete successfully' ); ?>';
                            }
                            $('.ad5-loyalty-form-message-success').html( message ).show();
                        } else {
                            Object.keys(response.errors).forEach(function(key){
                                var val = this[key];
                                $('.ad5-loyalty-form-error[data-name='+key+']').html(val).show();
                            }, response.errors)
                        }
                    }
                }
                $('.ad5-loyalty-button-submit').prop('disabled', false).removeClass('ad5-loyalty-button-disabled');
			}
		});
        return false;
	});
})(jQuery)
</script>
