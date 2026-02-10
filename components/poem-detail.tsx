"use client"

import type React from "react"

import { useState } from "react"
import { useRouter } from "next/navigation"
import type { User } from "@supabase/supabase-js"
import { createClient } from "@/lib/supabase/client"
import { Card, CardContent, CardHeader } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Textarea } from "@/components/ui/textarea"
import { Heart, MessageCircle, FileIcon, ArrowLeft, Loader2 } from "lucide-react"
import { formatDistanceToNow } from "date-fns"
import Link from "next/link"

interface Comment {
  id: string
  content: string
  created_at: string
  profiles: {
    username: string
    display_name: string
    avatar_url: string | null
  }
}

interface PoemDetailProps {
  poem: {
    id: string
    title: string
    content: string
    format: string
    file_url: string | null
    file_name: string | null
    author_id: string
    author_username: string
    author_display_name: string
    author_avatar_url: string | null
    likes_count: number
    comments_count: number
    created_at: string
  }
  comments: Comment[]
  isLiked: boolean
  currentUser: User | null
}

export function PoemDetail({ poem, comments: initialComments, isLiked: initialIsLiked, currentUser }: PoemDetailProps) {
  const [isLiked, setIsLiked] = useState(initialIsLiked)
  const [likesCount, setLikesCount] = useState(poem.likes_count)
  const [comments, setComments] = useState(initialComments)
  const [newComment, setNewComment] = useState("")
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [isLiking, setIsLiking] = useState(false)
  const router = useRouter()

  const handleLike = async () => {
    if (!currentUser) {
      router.push("/auth/login")
      return
    }

    setIsLiking(true)
    const supabase = createClient()

    try {
      const { data: profile } = await supabase.from("profiles").select("id").eq("id", currentUser.id).single()

      if (!profile) throw new Error("Profile not found")

      if (isLiked) {
        // Unlike
        await supabase.from("likes").delete().eq("user_id", profile.id).eq("poem_id", poem.id)
        setIsLiked(false)
        setLikesCount((prev) => prev - 1)
      } else {
        // Like
        await supabase.from("likes").insert({
          user_id: profile.id,
          poem_id: poem.id,
        })
        setIsLiked(true)
        setLikesCount((prev) => prev + 1)
      }
    } catch (error) {
      console.error("Error toggling like:", error)
    } finally {
      setIsLiking(false)
    }
  }

  const handleCommentSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!currentUser || !newComment.trim()) return

    setIsSubmitting(true)
    const supabase = createClient()

    try {
      const { data: profile } = await supabase.from("profiles").select("*").eq("id", currentUser.id).single()

      if (!profile) throw new Error("Profile not found")

      const { data: comment, error } = await supabase
        .from("comments")
        .insert({
          user_id: profile.id,
          poem_id: poem.id,
          content: newComment.trim(),
        })
        .select()
        .single()

      if (error) throw error

      // Add the new comment to the list
      setComments([
        {
          id: comment.id,
          content: comment.content,
          created_at: comment.created_at,
          profiles: {
            username: profile.username,
            display_name: profile.display_name,
            avatar_url: profile.avatar_url,
          },
        },
        ...comments,
      ])
      setNewComment("")
      router.refresh()
    } catch (error) {
      console.error("Error posting comment:", error)
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <div className="space-y-6">
      <Button variant="ghost" asChild>
        <Link href="/">
          <ArrowLeft className="mr-2 h-4 w-4" />
          Back to Feed
        </Link>
      </Button>

      <Card>
        <CardHeader className="space-y-4">
          <div className="flex items-center gap-3">
            <Avatar className="h-12 w-12">
              <AvatarImage src={poem.author_avatar_url || undefined} />
              <AvatarFallback>{poem.author_display_name[0]?.toUpperCase()}</AvatarFallback>
            </Avatar>
            <div>
              <p className="font-medium">{poem.author_display_name}</p>
              <p className="text-sm text-muted-foreground">@{poem.author_username}</p>
            </div>
          </div>
          <h1 className="text-3xl font-bold">{poem.title}</h1>
          <p className="text-sm text-muted-foreground">
            {formatDistanceToNow(new Date(poem.created_at), { addSuffix: true })}
          </p>
        </CardHeader>
        <CardContent className="space-y-6">
          {poem.format === "text" ? (
            <div className="prose prose-lg max-w-none">
              <p className="whitespace-pre-line font-serif leading-relaxed">{poem.content}</p>
            </div>
          ) : poem.format === "image" && poem.file_url ? (
            <div className="rounded-lg overflow-hidden">
              <img src={poem.file_url || "/placeholder.svg"} alt={poem.title} className="w-full" />
            </div>
          ) : poem.file_url ? (
            <a
              href={poem.file_url}
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center gap-3 p-4 border rounded-lg hover:bg-muted transition-colors"
            >
              <FileIcon className="h-8 w-8 text-muted-foreground" />
              <div>
                <p className="font-medium">{poem.file_name}</p>
                <p className="text-sm text-muted-foreground">Click to view document</p>
              </div>
            </a>
          ) : null}

          <div className="flex items-center gap-4 pt-4 border-t">
            <Button
              variant={isLiked ? "default" : "outline"}
              size="sm"
              onClick={handleLike}
              disabled={isLiking}
              className="gap-2"
            >
              <Heart className={`h-4 w-4 ${isLiked ? "fill-current" : ""}`} />
              {likesCount}
            </Button>
            <div className="flex items-center gap-2 text-sm text-muted-foreground">
              <MessageCircle className="h-4 w-4" />
              {comments.length} {comments.length === 1 ? "comment" : "comments"}
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <h2 className="text-xl font-bold">Comments</h2>
        </CardHeader>
        <CardContent className="space-y-6">
          {currentUser ? (
            <form onSubmit={handleCommentSubmit} className="space-y-3">
              <Textarea
                placeholder="Share your thoughts..."
                value={newComment}
                onChange={(e) => setNewComment(e.target.value)}
                rows={3}
              />
              <Button type="submit" disabled={isSubmitting || !newComment.trim()}>
                {isSubmitting ? (
                  <>
                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                    Posting...
                  </>
                ) : (
                  "Post Comment"
                )}
              </Button>
            </form>
          ) : (
            <div className="text-center py-4 bg-muted rounded-lg">
              <p className="text-muted-foreground">
                <Link href="/auth/login" className="underline hover:text-primary">
                  Sign in
                </Link>{" "}
                to leave a comment
              </p>
            </div>
          )}

          {comments.length === 0 ? (
            <p className="text-center text-muted-foreground py-8">
              No comments yet. Be the first to share your thoughts!
            </p>
          ) : (
            <div className="space-y-4">
              {comments.map((comment) => (
                <div key={comment.id} className="flex gap-3 p-4 bg-muted rounded-lg">
                  <Avatar className="h-10 w-10">
                    <AvatarImage src={comment.profiles.avatar_url || undefined} />
                    <AvatarFallback>{comment.profiles.display_name[0]?.toUpperCase()}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <p className="font-medium">{comment.profiles.display_name}</p>
                      <p className="text-sm text-muted-foreground">@{comment.profiles.username}</p>
                      <span className="text-sm text-muted-foreground">·</span>
                      <time className="text-sm text-muted-foreground">
                        {formatDistanceToNow(new Date(comment.created_at), { addSuffix: true })}
                      </time>
                    </div>
                    <p className="text-sm whitespace-pre-line">{comment.content}</p>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
