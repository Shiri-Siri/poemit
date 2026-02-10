-- Create view for poems with like counts and author info
create or replace view public.poems_with_stats as
select 
  p.*,
  profiles.username as author_username,
  profiles.display_name as author_display_name,
  profiles.avatar_url as author_avatar_url,
  coalesce(like_counts.count, 0) as likes_count,
  coalesce(comment_counts.count, 0) as comments_count
from public.poems p
left join public.profiles on p.author_id = profiles.id
left join (
  select poem_id, count(*) as count
  from public.likes
  group by poem_id
) like_counts on p.id = like_counts.poem_id
left join (
  select poem_id, count(*) as count
  from public.comments
  group by poem_id
) comment_counts on p.id = comment_counts.poem_id;
