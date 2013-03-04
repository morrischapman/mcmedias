{if isset($collection)}
<ul>
  {foreach from=$collection->getFiles() item=file}
  <li><a href="{$file->getUrl()}">{$file->getOriginalFilename()}</a></li>
  {/foreach}
</ul>
{/if}