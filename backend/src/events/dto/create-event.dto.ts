import { IsString, IsDateString, IsNumber, IsOptional, Min, Max } from 'class-validator';

export class CreateEventDto {
  @IsString()
  title: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsDateString()
  date: string;

  @IsNumber()
  @Min(15)
  @Max(480) // máximo 8 horas
  duration: number;

  @IsOptional()
  @IsString()
  location?: string;

  @IsOptional()
  @IsString()
  type?: string;

  @IsOptional()
  @IsNumber()
  @Min(1)
  maxAttendees?: number;

  @IsOptional()
  preguntas?: string[];
} 