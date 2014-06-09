{capture name=path}{l s='Rastreamento correios' mod='myrtillerastreacorreio'}{/capture}
<style type="text/css">
	#myr_rc h1 {
		margin-bottom: 20px;
	}
	#myr_rc li {
		padding: 10px;
		border-bottom: 1px dashed;
		margin-bottom: 20px;
		background-color: #f9f9f9;
	}
	#myr_rc li:before,
	#myr_rc li:after {
		content: ' ';
		display: table;
		webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	#myr_rc .last_status {
		font-size: 1.2em;
		font-weight: bold;
		padding: 30px 10px;
		box-shadow: 0px 0px 10px 0px #aaa;
		border: none;
	}
	#myr_rc .number {
		font-size: 2em;
		float: left;
		padding: 2px 5px;
		margin-right: 10px;
	}
	#myr_rc .status_content {
		overflow: hidden;
	}
</style>
<div id="myr_rc">
	<h1>{l s='Rastreamento N°' mod="myrtillerastreacorreio"} : {$tracking_number}</h1>

	{if $tracking_number_status}
	{assign var="first_item" value="true"}
	<ul>
	{foreach from=$tracking_number_status key=k item=status}
		<li id="status_{$k}" {if $first_item}class="last_status"{/if}>
			<div class="number">{$k}</div>
			<div class="status_content">{$status}</div>
		</li>
		{assign var="first_item" value=null}
	{/foreach}
	</ul>
	{else}
		<p>{l s='Não temos dados para este numero !' mod="myrtillerastreacorreio"}</p>
	{/if}
</div>