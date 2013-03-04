{if isset($collection)}
<div id="collection_{$collection->getId()}">
  {if $collection->hasFiles()}
  <table cellspacing="0" class="pagetable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Filename</th>
            <th class="pageicon">&nbsp;</th>
        </tr>
    </thead>
    <tbody class="sortable" data-url="{$sort}">
      {foreach from=$collection->getFiles() item=file}
      <tr class="{cycle values="row1,row2"}" onmouseover="this.className='{cycle values="row1,row2"}hover';" onmouseout="this.className='{cycle values="row1,row2"}';" data-id="{$id}media_id_{$file->getId()}">
        <td><div class="editable" id="media_{$file->getId()}">{$file->getTitle()}</div></td>
        <td>{$file->getOriginalFilename()}</td>
        <td><a href="{$delete}&amp;{$id}file_id={$file->getId()}&amp;suppressoutput=1&amp;showtemplate=0" class="delete">{$delete_icon}</a></td>
      </tr>
      {/foreach}
    </tbody>
  </table>
  {/if}
</div>
{/if}