<% if Segment %>
  <% with Segment %>
    $RenderSegmentForm($Up.ID, $Up.ClassName)
  <% end_with %>
<% end_if %>
