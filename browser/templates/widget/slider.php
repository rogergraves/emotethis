<?php
	$a_nav_dots = array();
?>

	<div id="short-stimulus"><?= $tpl_vars['survey']->getShortStimulus() ?></div>
	<div id="instruction-header" ><div id="instruction-header-text">Move mouse up and down near emoticon to select intensity. Click to set.</div></div>
	<div id="slider-nav">
		<div id="back-button">
			<img id="back-button-on" src="../images/browser/left-arrow.png" usemap="#back-button-map">
			<map name="back-button-map" >
				<area shape="poly" coords="0,57,19,111,19,2" href="#"/>
			</map>
		</div>
		<div id="next-button">
			<img id="next-button-on" src="../images/browser/right-arrow.png" usemap="#next-button-map">
			<map name="next-button-map">
				<area shape="poly" coords="0,2,0,111,19,57" href="#"/>
			</map>
		</div>
		<div id="slider" class="slider">
			<ul>
				<?php
					if(! ( array_key_exists('not_welcome',$tpl_vars) && $tpl_vars['not_welcome'])){
				?>
				<!-- Welcome page -->
				<li id="welcome-slide">
					<?php array_push($a_nav_dots,'welcome'); ?>
					<div class="short-desc-value">Welcome to e.mote&#0153;</div>
					<div class="face-name"> &nbsp; </div>
					<div class="instruction">
						<div class="welcome-text">
You are about to <span class="bold-text">e.mote</span>&#0153;, a fast and fun way to say how you feel about the things you see, do, and experience.
							<br/><br/>
							And no worries, your <span class="bold-text">e.mote</span>&#0153; responses are anonymous
							 so you can express how you really feel. <br/><br/>
							Enjoy <span class="bold-text">"e.moting!"</span>.<br/><br/>
							<!-- The <span class="bold-text">e.mote</span>&#0153; Team. -->
						</div>
						<div class="welcome-start" >
							<div id="team-text">The <span class="bold-text">e.mote&#0153;</span> Team</div>
							<a href="#" id="get-started"><img src="../images/browser/get_started_button.png"></a>
							<div class="clear-both"></div>
						</div>
					</div>
				</li>
				<?php
					}
				?>

				<?php if(0){ ?>
				<!-- Page stimulus -->
				<li id="stimulus-slide">
					<?php array_push($a_nav_dots,'stimulus'); ?>
					<div class="short-desc-value">&nbsp;</div>
					<div class="face-name"> &nbsp; </div>
					<div class="instruction">
						<div class="instruction-body">
							<div class="instruction-text">
								<?= $tpl_vars['survey']->getStimulus() ?>
							</div>
							<!--
							<div class="instruction-img">
								<img src="<?= $tpl_vars['survey']->getItemImage() ?>">
							</div>
							-->
							<div class="clear-both"></div>
						</div>
					</div>
				</li>
				<?php } ?>

				<!-- Page emote -->

				<li id="emote-slide">
					<?php array_push($a_nav_dots,'emote'); ?>
					<div class="short-desc-value"> Move your mouse over the face to change the emotion. Click to set. </div>


					<div class="instruction-emote2">
						<div class="stimulus-short">
							Tell us how you feel about <span class="bold-text"><?= $tpl_vars['survey']->getShortStimulus() ?></span>.
						</div>
					</div>
					<div id="emote-picker" class="face-win" style="margin-top: 10px;">
						<div id="emote-face"></div>
						<div id="emote-mouse-follow" class="mouse-follow"></div>
						<div class="face-shadow"></div>
						<div id="emote-face-name" > &nbsp; </div>
					</div>
				</li>

				<!-- Page intensity -->

				<li id="intensity-slide">
					<?php array_push($a_nav_dots,'intensity'); ?>
					<div class="short-desc-value"> Move mouse up and down near emoticon to select intensity. Click to set. </div>
					<div id="intensity-face-name" class="face-name"> &nbsp; </div>

					<div class="instruction-emote">
						<div id="intensity-stimulus" class="stimulus-short">
							Just how <span id="current-face-name">&nbsp;</span> does <br/> <span class="bold-text"><?= $tpl_vars['survey']->getShortStimulus() ?></span> make you feel?
						</div>
					</div>

					<div id="intensity-bound-top"></div>

					<div id="intensity-picker" class="iface-win">
						<div id="intensity-face"></div>
						<div id="intensity-mouse-follow" class="mouse-follow"></div>
						<div class="face-shadow"></div>
						<div id="intensity-bg2"></div>
					</div>



					<div id="intensity-bound-bottom"></div>

				</li>

				<!-- Page verbatim -->

				<li id="verbatim-slide">
					<?php array_push($a_nav_dots,'verbatim'); ?>
					<div class="short-desc-value"> &nbsp; </div>
					<div class="face-name"> &nbsp; </div>
					<div class="instruction-emote">
						<div id="verbatim-stimulus">
							<div id="verbatim-text">
								You said <span class="bold-text"><?= $tpl_vars['survey']->getShortStimulus() ?></span> makes you feel
								<span id="verbatim-face-name">&nbsp;</span>. Why?
							</div>
							<div id="verbatim-image">
								<img id="verbatim-image-id" src="../images/browser/small/amazed_intensity_3.png">
							</div>
							<div class="clear-both"></div>
						</div>
					</div>

					<div class="verbatim-win">
						<div id="verbatim-textfield">
							<form action="">

								<textarea id="verbatim-textarea" >
								</textarea>
								<?php
								/*
								<span id="verbatim-textarea-text" style="display: none;">I felt <span id="verbatim-textarea-text-word">&nbsp;</span> about the <?= $tpl_vars['survey']->getShortStimulus() ?> because...</span>
								*/
								?>
								<span id="verbatim-textarea-text" style="display: none;">Because...</span>
								<div id="verbatim-button">
									<a id="submit-survey-data" href="#"><img src="../images/browser/verbatim_submit_button.png"></a>
									<!--  <input type="image" src="../images/browser/verbatim_submit_button.png" /> -->
								</div>
							</form>
						</div>
					</div>
				</li>

				<?php
					$has_demo = $tpl_vars['survey']->hasDemo();
					if($has_demo){
				?>
				<!-- Demo start -->

				<li id="demo-start-slide">
					<?php array_push($a_nav_dots,'demo-start'); ?>
					<div class="short-desc-value"> &nbsp; </div>
					<div class="demo-bg-win">
						<div id="demo-start-text">
							<div class="bold-text">Would you be interested in speaking with someone in order to provide more detailed feedback?</div><br/>
							Your information will remain anonymous and only displayed as part of a larger set of data.
						</div>
						<div id="demo-start-part1">

							<a id="demo-yes-button" href="#"><img src="../images/browser/take_demos_yes_button.png"></a>
							<a id="demo-no-button" href="#"><img src="../images/browser/take_demos_no_button.png"></a>
						</div>
						<div id="demo-start-part2">
							<form id="user-data-form">
								<label for="name" >Name: </label>
								<input type="text" name="name" id="name" value=""><div class="clear-both"></div>
								<label for="email" >Email: </label>
								<input type="text" name="email" id="email" value=""><div class="clear-both"></div>
								<label for="phone" >Phone: </label>
								<input type="text" name="phone" id="phone" value=""><div class="clear-both"></div>
							</form>
							<div class="submit-data-demo">
								<a id="submit-data-button" href="#"><img src="../images/browser/data-submit-button.png"></a>
							</div>
						</div>
					</div>
				</li>
				<?php
					}
				?>


				<?php
					$a_demo = $tpl_vars['survey']->getDemo();
					if($a_demo){
						array_push($a_nav_dots,'demo');
				?>


				<!-- Demo page -->
				<li id="demo-slide">
					<div class="short-desc-value"> &nbsp; </div>

					<div class="demo-win">

						<div class="question-win">
						<div id="demo-form-error">&nbsp;</div>
						<form id="demo-form">
						<?php

							foreach($a_demo as $demo){
						?>
							<div class="demo-title"><?= $demo['block-text'] ?></div>
							<?php
								foreach($demo['questions'] as $question){
							?>

								<?php
									if($question['type'] == 'text'){
								?>
								<div class="demo-input-error" id="<?= $question['id'] ?>-error"></div>
								<div class="question-text"><?= $question['title'] ?></div>
								<input type="text" name="<?= $question['id'] ?>" class="input-text" value="">
								<div class="clear-both"></div>
								<?php
									}else if($question['type'] == 'radio'){
								?>
								<div class="demo-input-error" id="<?= $question['id'] ?>-error"> &nbsp; </div>
								<div class="radio-text"><?= $question['title'] ?></div>
									<div class="input-radio">
									<?php

										foreach($question['values'] as $value){
									?>
										<div class="radio-block"><input type="radio" name="<?= $question['id'] ?>" value="<?php if(array_key_exists('value',$value)) print $value['value']; ?>"><?= $value['title'] ?></div>
									<?php
										}
									?>
									</div>
							<?php
									}else if($question['type'] == 'checkbox'){
							?>
								<div class="demo-input-error" id="<?= $question['id'] ?>-error"> &nbsp; </div>
								<div class="checkbox-text"><?= $question['title'] ?></div>
									<div class="input-checkbox">
									<?php

										foreach($question['values'] as $value){
									?>
										<div class="checkbox-block"><input type="checkbox" name="<?= $question['id'] ?>" value="<?php if(array_key_exists('value',$value)) print $value['value']; ?>"><?= $value['title'] ?></div>
									<?php
										}
									?>
									</div>
							<?php
									}
								}
							?>
						<?php
							}
						?>
						</form>
						</div>
						<div class="submit-demo">
							<a id="submit-demo-button" href="#"><img src="../images/browser/verbatim_submit_button.png"></a>
						</div>
						<?php
						$a_question = array();
						foreach($a_demo as $demo){
							foreach($demo['questions'] as $question){
								//array_push($a_question,array($question['id'] => "required"));
								$a_question[$question['id']] = "required";
							}
						}
						$rule_validate = json_encode($a_question);
						?>

						<script>
							var demoValidateRules = <?php print $rule_validate; ?>;
						</script>

					</div>
				</li>
				<?php
					}
				?>
				<!-- Thanks page -->

				<li id="thanks-slide">
					<?php array_push($a_nav_dots,'thanks'); ?>
					<div class="short-desc-value"> &nbsp; </div>
					<div class="thanks-bg-win">
						<div class="share-block">
							<a id="facebook_url" href="#"><img src="../images/browser/facebook_icon.png"></a>
							<a id="twitter_url" href="#" target="_blank"><img src="../images/browser/twitter_icon.png"></a>
						</div>
					</div>
				</li>

			</ul>

		</div>

	<div class="clear-both"></div>
	</div>
		<div id="dots-nav">
			<?php
				$num_dots = 0;
				foreach($a_nav_dots as $dot_id){
					if($num_dots){
			?>
						<img id="dot-<?= $dot_id ?>" src="../images/browser/white_dot.png">
			<?php
					}else{
			?>
						<img id="dot-<?= $dot_id ?>" src="../images/browser/yellow_dot.png">
			<?php
					}
					++$num_dots;
				}
			?>
		</div>

<?php
	include(TEMPLATE_DIR . 'loader.php');
	include(TEMPLATE_DIR . 'preload_images.php');
?>
