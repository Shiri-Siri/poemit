import { createClient } from "@/lib/supabase/server"
import { PoemFeed } from "@/components/poem-feed"
import { Hero } from "@/components/hero"

export default async function HomePage() {
  const supabase = await createClient()

  const {
    data: { user },
  } = await supabase.auth.getUser()

  // Fetch poems with stats using the view
  const { data: poems } = await supabase.from("poems_with_stats").select("*").order("created_at", { ascending: false })

  // Get user's liked poems if authenticated
  let likedPoemIds: string[] = []
  if (user) {
    const { data: profile } = await supabase.from("profiles").select("id").eq("id", user.id).single()
    if (profile) {
      const { data: likes } = await supabase.from("likes").select("poem_id").eq("user_id", profile.id)
      likedPoemIds = likes?.map((like) => like.poem_id) || []
    }
  }

  return (
    <div className="min-h-screen">
      <Hero user={user} />
      <div className="container mx-auto px-4 py-12">
        <PoemFeed poems={poems || []} likedPoemIds={likedPoemIds} currentUserId={user?.id} />
      </div>
    </div>
  )
}
