import { Button } from "@/components/ui/button"
import { Feather } from "lucide-react"
import Link from "next/link"
import type { User } from "@supabase/supabase-js"

interface HeroProps {
  user: User | null
}

export function Hero({ user }: HeroProps) {
  return (
    <div className="border-b bg-gradient-to-b from-background to-muted/20">
      <div className="container mx-auto px-4 py-16 md:py-24">
        <div className="flex flex-col items-center text-center space-y-6">
          <div className="inline-flex items-center justify-center p-3 bg-primary/10 rounded-full">
            <Feather className="h-8 w-8 text-primary" />
          </div>
          <h1 className="text-4xl md:text-6xl font-bold tracking-tight text-balance max-w-3xl">
            Your Stage for Poetry
          </h1>
          <p className="text-lg md:text-xl text-muted-foreground max-w-2xl text-balance">
            Write, share, and discover poems from passionate poets around the world. Every voice deserves to be heard.
          </p>
          <div className="flex flex-wrap gap-3 justify-center">
            {user ? (
              <Button asChild size="lg">
                <Link href="/write">
                  <Feather className="mr-2 h-5 w-5" />
                  Write a Poem
                </Link>
              </Button>
            ) : (
              <>
                <Button asChild size="lg">
                  <Link href="/auth/signup">Get Started</Link>
                </Button>
                <Button asChild size="lg" variant="outline">
                  <Link href="/auth/login">Sign In</Link>
                </Button>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
