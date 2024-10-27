<div class="wrap">

<h2>WP LOYALTY by AD5</h2>

<?php AD5_Loyalty_Admin::admin_page_menu( 'ad5-loyalty-docs' ); ?>

<h2><?php $this->e( 'Shortcodes' ); ?></h2>

<code>[loyalty_button_register]</code>

<p><?php $this->e( 'display sign-up button.' ); ?></p>

<code>[loyalty_button_register text="<?php $this->e( 'Free Sign Up' ); ?>"]</code>

<p><?php $this->e( 'To change button text, use [text] attribute.' ); ?></p>

<code>[loyalty_button_register class="your-button-class"]</code>

<p><?php $this->e( 'To change button style with your CSS, use [class] attribute.' ); ?></p>

<code>[loyalty_button_login]</code>

<p><?php $this->e( 'display sign-in button.' ); ?></p>

<code>[loyalty_button_login text="<?php $this->e( 'Sign In And Checkout!' ); ?>" class="your-button-class"]</code>

<p><?php $this->e( 'Attributes [text] and [class] are also available.' ); ?></p>

<h2><?php $this->e( 'Use in theme' ); ?></h2>

<p><?php $this->e( 'To display button in theme files, mark up a element with href and class attributes like below:' ); ?></p>

<code>&lt;a href="#ad5-loyalty-register" class="ad5-loyalty-button"&gt;<?php $this->e( 'Sign Up' ); ?>&lt;/a&gt;</code><br>
<code>&lt;a href="#ad5-loyalty-signin" class="ad5-loyalty-button"&gt;<?php $this->e( 'Sign In' ); ?>&lt;/a&gt;</code>

<p><?php $this->e( 'To display content only for members or guests, function is_user_logged_in() is available.' ); ?></p>

<code>&lt;? if( is_user_logged_in() ): ?&gt;</code><br>
<code>//<?php $this->e( 'contents only for members' ); ?></code><br>
<code>&lt;? endif; ): ?&gt;</code><br>
<br>
<code>&lt;? if( ! is_user_logged_in() ): ?&gt;</code><br>
<code>//<?php $this->e( 'contents only for guests' ); ?></code><br>
<code>&lt;? endif; ): ?&gt;</code><br>
