-- Create likes table
create table if not exists public.likes (
  id uuid primary key default gen_random_uuid(),
  user_id uuid not null references public.profiles(id) on delete cascade,
  poem_id uuid not null references public.poems(id) on delete cascade,
  created_at timestamp with time zone default now(),
  unique(user_id, poem_id)
);

-- Enable RLS
alter table public.likes enable row level security;

-- RLS Policies for likes
create policy "likes_select_all"
  on public.likes for select
  using (true);

create policy "likes_insert_own"
  on public.likes for insert
  with check (auth.uid() = user_id);

create policy "likes_delete_own"
  on public.likes for delete
  using (auth.uid() = user_id);

-- Create indexes
create index if not exists idx_likes_poem_id on public.likes(poem_id);
create index if not exists idx_likes_user_id on public.likes(user_id);
