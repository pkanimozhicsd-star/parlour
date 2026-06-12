-- Run this in Supabase: SQL Editor → New query → Run

create table if not exists public.quote_requests (
  id uuid primary key default gen_random_uuid(),
  submitted_at text not null,
  name text not null,
  mobile text not null,
  email text not null,
  service text not null,
  created_at timestamptz default now()
);

alter table public.quote_requests enable row level security;

create policy "Allow anonymous insert on quote_requests"
  on public.quote_requests
  for insert
  to anon
  with check (true);
