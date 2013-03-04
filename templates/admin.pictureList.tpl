{if isset($collection)}
<div id="collection_{$collection->getId()}">
  {if $collection->hasFiles()}
  <ul class="sortable" data-url="{$sort}">
    {foreach from=$collection->getFiles() item=file}
    <li style="float: left; border: 1px solid gray; padding: 0px; margin: 5px; list-style-type: none;" data-id="{$id}media_id_{$file->getId()}">
      
        <a href="{$file->getUrl()}" target="_new" rel="external"><img src="{MCMedias action="thumbnail" width=150 height=150 file=$file->getRelativePath()}" alt="{$file->getOriginalFilename()}" /></a>
        <div style="padding: 2px; background-color: #CCC;">
          <div class="editable" id="media_{$file->getId()}">{$file->getTitle()}</div>
          <a href="{$delete}&amp;{$id}file_id={$file->getId()}&amp;suppressoutput=1&amp;showtemplate=0" class="delete" style="float: right;">{$delete_icon}</a>
          <div style="clear:both;"></div>
        </div>      
    </li>
    {/foreach}
  </ul>
  <div style="clear: both;"></div>
  {/if}
</div>
{/if}