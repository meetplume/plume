import * as React from "react"

import { cn } from "@/lib/utils"

function Hero({ className, ...props }: React.ComponentProps<"section">) {
  return (
    <section
      data-slot="hero"
      className={cn(
        "flex flex-col-reverse items-center gap-8 py-12 md:flex-row md:gap-16 md:py-20",
        className
      )}
      {...props}
    />
  )
}

function HeroContent({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="hero-content"
      className={cn("flex flex-1 flex-col gap-6", className)}
      {...props}
    />
  )
}

function HeroTitle({ className, ...props }: React.ComponentProps<"h1">) {
  return (
    <h1
      data-slot="hero-title"
      className={cn(
        "text-4xl font-bold tracking-tight md:text-5xl lg:text-6xl text-balance",
        className
      )}
      {...props}
    />
  )
}

function HeroTagline({ className, ...props }: React.ComponentProps<"p">) {
  return (
    <p
      data-slot="hero-tagline"
      className={cn("text-muted-foreground text-lg md:text-xl text-balance", className)}
      {...props}
    />
  )
}

function HeroActions({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="hero-actions"
      className={cn("flex flex-wrap items-center gap-3", className)}
      {...props}
    />
  )
}

function HeroImage({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      data-slot="hero-image"
      className={cn("flex shrink-0 items-center justify-center", className)}
      {...props}
    />
  )
}

export { Hero, HeroContent, HeroTitle, HeroTagline, HeroActions, HeroImage }
