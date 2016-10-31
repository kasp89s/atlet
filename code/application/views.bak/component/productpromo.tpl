{getconfig path="core.url_suffix" assign="url_suffix"}
<tr>
	<td style="background: url('/i/menubg1_3.jpg');" width="274px" height="284px" valign="top">
		<table width="270px" height="244px">

			<tr>
				<td valign="top" width="{$data.image_width}" height="{$data.image_height}" style="background: url('/files/catalog/photo/{$data.id}/0/{$data.uri}.{$data.ext}') no-repeat bottom" class="iePNG">
					<font class="art" style="width:100%;text-align:right;">
						<br/>
						<div align="right">
							Артикул:
							<br/>
							<a href="{$catalog_uri_base}{$data.sect_uri}/{$data.uri}{$url_suffix}" class="pic">{$data.code}</a>
						</div>
					</font>
				</td>
				<td valign="top">
				</td>
			</tr>
		</table>
	</td>
</tr>