import { IsNumber, IsOptional, IsString, Min, Max } from 'class-validator';

export class CreateEvaluationDto {
  @IsNumber()
  eventId: number;

  @IsOptional()
  @IsString()
  feedback?: string;
}

export class GradeEvaluationDto {
  @IsNumber()
  @Min(1)
  @Max(10)
  score: number;

  @IsOptional()
  @IsString()
  feedback?: string;
} 