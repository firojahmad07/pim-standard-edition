import React from 'react';
import {LoaderIcon, Locale as LocaleWithFlag} from 'akeneo-design-system';
import {Locale, LocaleCode, useIsMounted, useRouter} from '@akeneo-pim-community/shared';
import {LocaleRepository} from '../repositories';

type LocaleProps = {
  localeCode: LocaleCode;
};

const LocaleLabel: React.FC<LocaleProps> = ({localeCode}) => {
  const [locale, setLocale] = React.useState<Locale | null | undefined>(null);
  const isMounted = useIsMounted();
  const router = useRouter();

  React.useEffect(() => {
    LocaleRepository.find(router, localeCode).then(locale => {
      if (isMounted()) {
        setLocale(locale);
      }
    });
  }, [isMounted, localeCode, router]);

  if (locale === null) {
    return <LoaderIcon />;
  }

  if (locale === undefined) {
    return <>{localeCode}</>;
  }

  return <LocaleWithFlag code={locale.code} languageLabel={locale.language} />;
};

export {LocaleLabel};
