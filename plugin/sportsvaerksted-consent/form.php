<?php
/**
 * The form itself. Deliberately plain markup with almost no colour of its own:
 * inside an Enfold page it should pick up the theme's fonts and colours rather
 * than announce itself as a bolted-on plugin.
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="samtykke" id="samtykke" style="--samtykke-label: <?php echo esc_attr(samtykke_get('label_color')); ?>; --samtykke-text: <?php echo esc_attr(samtykke_get('text_color')); ?>;">

  <div class="samtykke-lang">
    <button type="button" class="samtykke-langbtn" data-lang="da" aria-pressed="true">Dansk</button>
    <button type="button" class="samtykke-langbtn" data-lang="en" aria-pressed="false">English</button>
  </div>

  <div class="samtykke-block">
    <h2 class="samtykke-title" id="sam-title"></h2>
    <p class="samtykke-who" id="sam-who"></p>
    <p class="samtykke-help" id="sam-intro"></p>
    <div id="sam-terms"></div>
  </div>

  <div class="samtykke-block">
    <h3 id="sam-hUses"></h3>
    <p class="samtykke-help" id="sam-usesHelp"></p>
    <div id="sam-uses"></div>
  </div>

  <div class="samtykke-block">
    <h3 id="sam-hYou"></h3>
    <div class="samtykke-two">
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lName"></span><input type="text" id="sam-name" autocomplete="name"></label>
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lBirth"></span><input type="date" id="sam-birth"></label>
    </div>
    <div class="samtykke-two">
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lEmail"></span><input type="email" id="sam-email" autocomplete="email"></label>
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lPhone"></span><input type="tel" id="sam-phone" autocomplete="tel"></label>
    </div>
  </div>

  <div class="samtykke-block">
    <h3 id="sam-hSign"></h3>
    <p class="samtykke-help" id="sam-signHelp"></p>
    <!-- The date of signing belongs beside the signature. It was in the
         "about you" block and he did not find it there - twice. -->
    <div class="samtykke-two">
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lToday"></span><input type="date" id="sam-today"></label>
      <span></span>
    </div>
    <canvas class="samtykke-pad" id="sam-pad"></canvas>
    <div class="samtykke-padrow">
      <span id="sam-padHint"></span>
      <button type="button" id="sam-clear"></button>
    </div>
    <label class="samtykke-tick">
      <input type="checkbox" id="sam-agree">
      <span id="sam-agreeText"></span>
    </label>
  </div>

  <div class="samtykke-block">
    <h3 id="sam-hGuardian"></h3>
    <p class="samtykke-help" id="sam-guardianWhy"></p>
    <div class="samtykke-two">
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lGName"></span><input type="text" id="sam-gname"></label>
      <label class="samtykke-f"><span class="samtykke-lab" id="sam-lGRel"></span><input type="text" id="sam-grel"></label>
    </div>
    <canvas class="samtykke-pad" id="sam-gpad"></canvas>
    <div class="samtykke-padrow">
      <span id="sam-gpadHint"></span>
      <button type="button" id="sam-gclear"></button>
    </div>
  </div>

  <div class="samtykke-block">
    <p class="samtykke-err" id="sam-err" hidden></p>
    <button type="button" class="samtykke-send" id="sam-send"></button>
    <div class="samtykke-done" id="sam-done" hidden></div>
  </div>

  <!-- Not for people. A robot filling this in gets a polite nothing. -->
  <div class="samtykke-hp" aria-hidden="true">
    <label>Website<input type="text" id="sam-website" tabindex="-1" autocomplete="off"></label>
  </div>
</div>
