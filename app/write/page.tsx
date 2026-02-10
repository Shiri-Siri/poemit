import { redirect } from "next/navigation"
import { createClient } from "@/lib/supabase/server"
import { PoemEditor } from "@/components/poem-editor"

export default async function WritePage() {
  const supabase = await createClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()

  if (!user) {
    redirect("/auth/login")
  }

  return (
    <div className="container mx-auto max-w-4xl px-4 py-8">
      <div className="mb-8">
        <h1 className="text-4xl font-bold mb-2">Write a Poem</h1>
        <p className="text-muted-foreground">Share your poetic voice with the world</p>
      </div>
      <PoemEditor userId={user.id} />
    </div>
  )
}
