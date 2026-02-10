import { createClient } from "@/lib/supabase/server"
import { notFound } from "next/navigation"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Card, CardContent } from "@/components/ui/card"
import { PoemCard } from "@/components/poem-card"

interface ProfilePageProps {
  params: Promise<{ username: string }>
}

export default async function ProfilePage({ params }: ProfilePageProps) {
  const { username } = await params
  const supabase = await createClient()

  const {
    data: { user },
  } = await supabase.auth.getUser()

  // Fetch profile
  const { data: profile } = await supabase.from("profiles").select("*").eq("username", username).single()

  if (!profile) {
    notFound()
  }

  // Fetch user's poems
  const { data: poems } = await supabase
    .from("poems_with_stats")
    .select("*")
    .eq("author_id", profile.id)
    .order("created_at", { ascending: false })

  // Get liked poems if viewing own profile
  let likedPoemIds: string[] = []
  if (user) {
    const { data: userProfile } = await supabase.from("profiles").select("id").eq("id", user.id).single()
    if (userProfile) {
      const { data: likes } = await supabase.from("likes").select("poem_id").eq("user_id", userProfile.id)
      likedPoemIds = likes?.map((like) => like.poem_id) || []
    }
  }

  const poemCount = poems?.length || 0
  const totalLikes = poems?.reduce((sum, poem) => sum + (poem.likes_count || 0), 0) || 0

  return (
    <div className="container mx-auto max-w-6xl px-4 py-8">
      <Card className="mb-8">
        <CardContent className="pt-6">
          <div className="flex flex-col md:flex-row gap-6 items-start">
            <Avatar className="h-24 w-24">
              <AvatarImage src={profile.avatar_url || undefined} />
              <AvatarFallback className="text-2xl">{profile.display_name[0]?.toUpperCase()}</AvatarFallback>
            </Avatar>
            <div className="flex-1 space-y-3">
              <div>
                <h1 className="text-3xl font-bold">{profile.display_name}</h1>
                <p className="text-muted-foreground">@{profile.username}</p>
              </div>
              {profile.bio && <p className="text-muted-foreground">{profile.bio}</p>}
              <div className="flex gap-6 text-sm">
                <div>
                  <span className="font-bold">{poemCount}</span>{" "}
                  <span className="text-muted-foreground">{poemCount === 1 ? "poem" : "poems"}</span>
                </div>
                <div>
                  <span className="font-bold">{totalLikes}</span>{" "}
                  <span className="text-muted-foreground">{totalLikes === 1 ? "like" : "likes"}</span>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="space-y-4">
        <h2 className="text-2xl font-bold">Poems</h2>
        {poems && poems.length > 0 ? (
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {poems.map((poem) => (
              <PoemCard key={poem.id} poem={poem} isLiked={likedPoemIds.includes(poem.id)} currentUserId={user?.id} />
            ))}
          </div>
        ) : (
          <Card>
            <CardContent className="py-12 text-center text-muted-foreground">
              No poems yet. Start writing to share your voice!
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  )
}
