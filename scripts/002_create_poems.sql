-- Create poems table
create table if not exists public.poems (
  id uuid primary key default gen_random_uuid(),
  author_id uuid not null references public.profiles(id) on delete cascade,
  title text not null,
  content text not null,
  format text not null check (format in ('text', 'image', 'document')),
  file_url text,
  file_name text,
  views_count integer default 0,
  created_at timestamp with time zone default now(),
  updated_at timestamp with time zone default now()
);

-- Enable RLS
alter table public.poems enable row level security;

-- RLS Policies for poems
create policy "poems_select_all"
  on public.poems for select
  using (true);

create policy "poems_insert_own"
  on public.poems for insert
  with check (auth.uid() = author_id);

create policy "poems_update_own"
  on public.poems for update
  using (auth.uid() = author_id);

create policy "poems_delete_own"
  on public.poems for delete
  using (auth.uid() = author_id);

-- Create indexes for better performance
create index if not exists idx_poems_author_id on public.poems(author_id);
create index if not exists idx_poems_created_at on public.poems(created_at desc);
