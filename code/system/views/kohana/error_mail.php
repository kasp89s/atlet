<?php defined('SYSPATH') OR die('No direct access allowed.');
?>
<style type="text/css">
	
	#kohana_error {
		background: #CFF292;
		font-size: 1em;
		font-family: sans-serif;
		text-align: left;
		color: #111;
	}
	
	#kohana_error h1, #kohana_error h2 {
		margin: 0;
		padding: 1em;
		font-size: 1em;
		font-weight: normal;
		background: #CFF292;
		color: #000000;
	}
	
	#kohana_error h1 a, #kohana_error h2 a {
		color: #000;
	}
	
	#kohana_error h2 {
		background: #CFF292;
		border-top: 1px dotted;
	}
	
	#kohana_error h3 {
		margin: 0;
		padding: 0.4em 0 0;
		font-size: 1em;
		font-weight: normal;
	}
	
	#kohana_error p {
		margin: 0;
		padding: 0.2em 0;
	}
	
	#kohana_error a {
		color: #1b323b;
	}
	
	#kohana_error pre {
		overflow: auto;
		white-space: pre-wrap;
	}
	
	#kohana_error table {
		width: 100%;
		display: block;
		margin: 0 0 0.4em;
		padding: 0;
		border-collapse: collapse;
		background: #fff;
	}
	
	#kohana_error table td {
		border: solid 1px #ddd;
		text-align: left;
		vertical-align: top;
		padding: 0.4em;
	}
	
	#kohana_error div.content {
		padding: 0.4em 1em 1em;
		overflow: hidden;
		border-top: 1px dotted;
	}
	
	#kohana_error pre.source {
		margin: 0 0 1em;
		padding: 0.4em;
		background: #fff;
		border: dotted 1px #b7c680;
		line-height: 1.2em;
	}
	
	#kohana_error pre.source span.line {
		display: block;
	}
	
	#kohana_error pre.source span.highlight {
		background: #f0eb96;
	}
	
	#kohana_error pre.source span.line span.number {
		color: #666;
	}
	
	#kohana_error ol.trace {
		display: block;
		margin: 0 0 0 2em;
		padding: 0;
		list-style: decimal;
	}
	
	#kohana_error ol.trace li {
		margin: 0;
		padding: 0;
	}
</style>
<div id="kohana_error">
	<h1>
		<span class="type">
<?php echo $type?> [ <?php echo $code?> ]:
		</span>
		<span class="message">
<?php echo $message?>
		</span>
		
<?php
	$host = trim($_SERVER['HTTP_HOST'], '/');
	$request = trim($_SERVER['REQUEST_URI'], '/');
	echo "<br>URL: http://{$host}/{$request}";
?>

	</h1>
	<div class="content">
		<p>
			<span class="file">
<?php echo Kohana_Exception::debug_path($file)?>[ <?php echo $line?> ]
			</span>
		</p>

<?php if (Kohana_Exception::$source_output AND $source_code = Kohana_Exception::debug_source($file, $line)) : ?>
		<pre class="source"><code><?php foreach ($source_code as $num => $row) : ?><span class="line <?php if ($num == $line) echo 'highlight' ?>"><span class="number"><?php echo $num ?></span><?php echo htmlspecialchars($row, ENT_NOQUOTES, Kohana::CHARSET) ?></span><?php endforeach ?></code></pre>
<?php endif ?>

<?php if (Kohana_Exception::$trace_output) : ?>
		<ol class="trace">
			<?php foreach (Kohana_Exception::trace($trace) as $i=>$step): ?>
			<li>
				<p>
					<span class="file">
						<?php if ($step['file']): $source_id = 'source'.$i; ?>
						<?php if (Kohana_Exception::$source_output AND $step['source']) : ?>
						<?php echo Kohana_Exception::debug_path($step['file'])?>[ <?php echo $step['line']?> ]
						<?php else : ?>
						<span class="file"><?php echo Kohana_Exception::debug_path($step['file'])?>[ <?php echo $step['line']?> ]</span>
						<?php endif ?>
						<?php else : ?>
						{<?php echo __('PHP internal call')?>}
						<?php endif?>
					</span>
					&raquo;
					<?php echo $step['function']?>(<?php if ($step['args'] && $i < 2): $args_id = 'args'.$i; ?><?php echo __('arguments')?>
<?php endif?>)
				</p>
				<?php if (isset($args_id)): ?>
				<div>
					<table cellspacing="0">
						<?php foreach ($step['args'] as $name=>$arg): ?>
						<tr>
							<td>
								<code>
<?php echo $name?>
								</code>
							</td>
							<td>
								<pre><?php echo nl2br(Kohana_Exception::dump($arg)); ?></pre>
							</td>
						</tr>
						<?php endforeach?>
					</table>
				</div>
				<?php endif?>
				<?php if (Kohana_Exception::$source_output AND $step['source'] AND isset($source_id)): ?>
				<pre class="source"><code><?php foreach ($step['source'] as $num => $row) : ?><span class="line <?php if ($num == $step['line']) echo 'highlight' ?>"><span class="number"><?php echo $num ?></span><?php echo htmlspecialchars($row, ENT_NOQUOTES, Kohana::CHARSET) ?><br></span><?php endforeach ?></code></pre>
				<?php endif?>
			</li>
			<?php unset($args_id, $source_id); ?>
			<?php endforeach?>
		</ol>
<?php endif ?>
	</div>
</div>
