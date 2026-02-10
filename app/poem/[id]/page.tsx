import { createClient } from "@/lib/supabase/server"
import { notFound } from "next/navigation"
import { PoemDetail } from "@/components/poem-detail"

interface PoemPageProps {
  params: Promise<{ id: string }>
}

export default async function PoemPage({ params }: PoemPageProps) {
  const { id } = await params
  const supabase = await createClient()

  const {
    data: { user },
  } = await supabase.auth.getUser()

  // Fetch poem with stats
  const { data: poem } = await supabase.from("poems_with_stats").select("*").eq("id", id).single()

  if (!poem) {
    notFound()
  }

  // Fetch comments
  const { data: comments } = await supabase
    .from("comments")
    .select(
      `
      *,
      profiles:user_id (
        username,
        display_name,
        avatar_url
      )
    `,
    )
    .eq("poem_id", id)
    .order("created_at", { ascending: false })

  let isLiked = false
  if (user) {
    const { data: profile } = await supabase.from("profiles").select("id").eq("id", user.id).maybeSingle()
    if (profile) {
      const { data: like } = await supabase
        .from("likes")
        .select("id")
        .eq("user_id", profile.id)
        .eq("poem_id", id)
        .maybeSingle()
      isLiked = !!like
    }
  }

  return (
    <div className="container mx-auto max-w-4xl px-4 py-8">
      <PoemDetail poem={poem} comments={comments || []} isLiked={isLiked} currentUser={user} />
    </div>
  )
}
