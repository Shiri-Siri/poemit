"use client"

import { PoemCard } from "@/components/poem-card"
import { Input } from "@/components/ui/input"
import { Search } from "lucide-react"
import { useState, useMemo } from "react"

interface Poem {
  id: string
  title: string
  content: string
  format: string
  file_url: string | null
  file_name: string | null
  author_username: string
  author_display_name: string
  author_avatar_url: string | null
  likes_count: number
  comments_count: number
  created_at: string
  author_id: string
}

interface PoemFeedProps {
  poems: Poem[]
  likedPoemIds: string[]
  currentUserId?: string
}

export function PoemFeed({ poems, likedPoemIds, currentUserId }: PoemFeedProps) {
  const [searchQuery, setSearchQuery] = useState("")

  const filteredPoems = useMemo(() => {
    if (!searchQuery.trim()) return poems

    const query = searchQuery.toLowerCase()
    return poems.filter(
      (poem) =>
        poem.title.toLowerCase().includes(query) ||
        poem.content.toLowerCase().includes(query) ||
        poem.author_username.toLowerCase().includes(query) ||
        poem.author_display_name.toLowerCase().includes(query),
    )
  }, [poems, searchQuery])

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-3xl font-bold">Discover Poems</h2>
          <p className="text-muted-foreground mt-1">Explore poetry from our community</p>
        </div>
      </div>

      <div className="relative max-w-md">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder="Search poems, authors..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          className="pl-10"
        />
      </div>

      {filteredPoems.length === 0 ? (
        <div className="text-center py-12">
          <p className="text-muted-foreground">
            {searchQuery ? "No poems found matching your search." : "No poems yet. Be the first to share!"}
          </p>
        </div>
      ) : (
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {filteredPoems.map((poem) => (
            <PoemCard
              key={poem.id}
              poem={poem}
              isLiked={likedPoemIds.includes(poem.id)}
              currentUserId={currentUserId}
            />
          ))}
        </div>
      )}
    </div>
  )
}
